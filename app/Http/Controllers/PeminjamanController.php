<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends Controller
{
    /**
     * Store a newly created peminjaman in storage.
     */
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
            'notes' => 'nullable|string|max:500',
        ]);

        $borrowedQty = $this->getOverlappingBorrowings(
            $item->id,
            $request->tanggal_pinjam,
            $request->tanggal_kembali
        );

        $availableStock = max(0, $item->stock - $borrowedQty);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $availableStock,
        ]);

        try {
            DB::beginTransaction();

            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status' => 'diajukan',
                'notes' => $request->notes,
            ]);

            DB::commit();

            Log::info('Peminjaman created', [
                'user_id' => Auth::id(),
                'peminjaman_id' => $peminjaman->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan peminjaman berhasil dikirim.',
                'peminjaman_id' => $peminjaman->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating peminjaman', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan.'
            ], 500);
        }
    }

    /**
     * Check availability
     */
    public function checkAvailability(Item $item, Request $request)
    {
        $quantity = $request->get('quantity', 1);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if (!$startDate || !$endDate) {
            return response()->json([
                'can_borrow' => false,
                'message' => 'Tanggal mulai dan akhir diperlukan.'
            ], 400);
        }

        $borrowedQty = $this->getOverlappingBorrowings(
            $item->id,
            $startDate,
            $endDate
        );

        $availableStock = max(0, $item->stock - $borrowedQty);

        return response()->json([
            'can_borrow' => $quantity <= $availableStock,
            'available_stock' => $availableStock,
            'requested_quantity' => $quantity,
            'total_stock' => $item->stock,
        ]);
    }

    /**
     * User history
     */
    public function history()
    {
        $peminjamans = Peminjaman::with(['item', 'item.category'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('peminjamans.history', compact('peminjamans'));
    }

    /**
     * Get user's borrowings
     */
    public function myBorrowings()
    {
        $peminjamans = Peminjaman::with(['item', 'item.category'])
            ->where('user_id', Auth::id())
            ->whereNotIn('status', ['dikembalikan', 'ditolak', 'dibatalkan'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('peminjamans.my-borrowings', compact('peminjamans'));
    }

    /**
     * Show peminjaman detail (USER / ADMIN)
     */
    public function show(Peminjaman $peminjaman)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        if ($peminjaman->user_id != $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $peminjaman->load(['item', 'item.category', 'user']);

        return view('peminjamans.show', compact('peminjaman'));
    }

    /**
     * Cancel peminjaman (ONLY OWNER)
     */
    public function cancel(Peminjaman $peminjaman)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        if ($peminjaman->user_id != $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if ($peminjaman->status !== 'diajukan') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya bisa membatalkan status diajukan.'
            ], 422);
        }

        try {
            $peminjaman->update(['status' => 'dibatalkan']);

            Log::info('Peminjaman cancelled', [
                'peminjaman_id' => $peminjaman->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman berhasil dibatalkan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Cancel error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan.'
            ], 500);
        }
    }

    /**
     * Helper: overlapping borrowings
     */
    private function getOverlappingBorrowings($itemId, $startDate, $endDate)
    {
        return Peminjaman::where('item_id', $itemId)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->where('tanggal_pinjam', '<=', $endDate)
            ->where('tanggal_kembali', '>=', $startDate)
            ->sum('quantity');
    }
}
