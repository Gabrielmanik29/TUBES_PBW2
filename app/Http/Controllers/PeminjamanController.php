<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    /**
     * Store a newly created peminjaman in storage.
     */
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $item->stock,
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Calculate available stock (stock yang tidak sedang dipinjam)
            $borrowedQty = Peminjaman::where('item_id', $item->id)
                ->whereIn('status', ['diajukan', 'disetujui'])
                ->sum('quantity');

            $availableStock = $item->stock - $borrowedQty;

            if ($request->quantity > $availableStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $availableStock
                ], 422);
            }

            // Create peminjaman record
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

            Log::info('Peminjaman created successfully', [
                'user_id' => Auth::id(),
                'peminjaman_id' => $peminjaman->id,
                'item_id' => $item->id,
                'quantity' => $request->quantity
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan peminjaman berhasil dikirim! Kami akan mengkonfirmasi dalam waktu singkat.',
                'peminjaman_id' => $peminjaman->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating peminjaman', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'item_id' => $item->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan peminjaman. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Check availability for a specific item
     */
    public function checkAvailability(Item $item, Request $request)
    {
        try {
            $quantity = $request->get('quantity', 1);
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            // Calculate current available stock
            $borrowedQty = Peminjaman::where('item_id', $item->id)
                ->whereIn('status', ['diajukan', 'disetujui'])
                ->sum('quantity');

            $availableStock = $item->stock - $borrowedQty;

            $canBorrow = $quantity <= $availableStock;

            return response()->json([
                'can_borrow' => $canBorrow,
                'available_stock' => $availableStock,
                'requested_quantity' => $quantity,
                'total_stock' => $item->stock,
                'message' => $canBorrow ?
                    "Barang tersedia untuk dipinjam ({$quantity} dari {$availableStock} stok tersedia)" :
                    "Stok tidak mencukupi. Stok tersedia: {$availableStock}"
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking availability', [
                'error' => $e->getMessage(),
                'item_id' => $item->id
            ]);

            return response()->json([
                'can_borrow' => false,
                'message' => 'Terjadi kesalahan saat memeriksa ketersediaan.'
            ], 500);
        }
    }

    /**
     * Get user's peminjaman history
     */
    public function history(Request $request)
    {
        $peminjamans = Peminjaman::with(['item', 'item.category'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('peminjamans.history', compact('peminjamans'));
    }

    /**
     * Show specific peminjaman details
     */
    public function show(Peminjaman $peminjaman)
    {
        // Ensure user can only view their own peminjaman or is admin
        if ($peminjaman->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $peminjaman->load(['item', 'item.category', 'user']);

        return view('peminjamans.show', compact('peminjaman'));
    }

    /**
     * Cancel peminjaman request
     */
    public function cancel(Peminjaman $peminjaman)
    {
        // Ensure user can only cancel their own peminjaman
        if ($peminjaman->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow cancellation for requested status
        if ($peminjaman->status !== 'diajukan') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya dapat membatalkan permintaan yang masih dalam status "Diajukan"'
            ], 422);
        }

        try {
            $peminjaman->update(['status' => 'dibatalkan']);

            Log::info('Peminjaman cancelled', [
                'user_id' => Auth::id(),
                'peminjaman_id' => $peminjaman->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permintaan peminjaman berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error cancelling peminjaman', [
                'error' => $e->getMessage(),
                'peminjaman_id' => $peminjaman->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan peminjaman'
            ], 500);
        }
    }
}
