<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function history()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $peminjamans = Peminjaman::where('user_id', $user->id)
            ->with(['item.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('peminjamans.history', compact('peminjamans'));
    }

    public function myBorrowings()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        $peminjamans = Peminjaman::where('user_id', $user->id)
            ->whereIn('status', [
                Peminjaman::STATUS_DIAJUKAN,
                Peminjaman::STATUS_DISETUJUI,
                Peminjaman::STATUS_DIPINJAM,
            ])
            ->with(['item.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('peminjamans.my-borrowings', compact('peminjamans'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
            'notes' => 'nullable|string',
        ]);

        $item = Item::findOrFail($request->item_id);

        // VALIDASI STOK
        $overlappingQty = Peminjaman::where('item_id', $item->id)
            ->whereIn('status', [
                Peminjaman::STATUS_DIAJUKAN,
                Peminjaman::STATUS_DISETUJUI,
                Peminjaman::STATUS_DIPINJAM,
            ])
            ->where('tanggal_pinjam', '<=', $request->tanggal_kembali)
            ->where('tanggal_kembali', '>=', $request->tanggal_pinjam)
            ->sum('quantity');

        $availableStock = max(0, $item->stock - $overlappingQty);

        if ($request->quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $availableStock,
            ], 422);
        }

        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'quantity' => $request->quantity,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => Peminjaman::STATUS_DIAJUKAN,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permintaan peminjaman berhasil dikirim.',
            'peminjaman_id' => $peminjaman->id,
        ]);
    }

    public function show(Peminjaman $peminjaman)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        if ($user->id !== $peminjaman->user_id && !$user->isAdmin()) {
            abort(403);
        }

        $peminjaman->load(['item.category', 'user']);

        return view('peminjamans.show', compact('peminjaman'));
    }

    public function cancel(Peminjaman $peminjaman)
    {
        $user = Auth::user();
        if (!$user || $user->id !== $peminjaman->user_id) {
            abort(403);
        }

        if ($peminjaman->status !== Peminjaman::STATUS_DIAJUKAN) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak dapat dibatalkan.',
            ], 422);
        }

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DIBATALKAN,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dibatalkan.',
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::findOrFail($request->item_id);

        $overlappingQty = Peminjaman::where('item_id', $item->id)
            ->whereIn('status', [
                Peminjaman::STATUS_DIAJUKAN,
                Peminjaman::STATUS_DISETUJUI,
                Peminjaman::STATUS_DIPINJAM,
            ])
            ->where('tanggal_pinjam', '<=', $request->tanggal_kembali)
            ->where('tanggal_kembali', '>=', $request->tanggal_pinjam)
            ->sum('quantity');

        $availableStock = max(0, $item->stock - $overlappingQty);

        return response()->json([
            'available' => $request->quantity <= $availableStock,
            'available_stock' => $availableStock,
        ]);
    }

    public function adminIndex()
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $peminjamans = Peminjaman::where('status', Peminjaman::STATUS_DIAJUKAN)
            ->with(['item.category', 'user'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.peminjamans.index', compact('peminjamans'));
    }

    public function approve(Peminjaman $peminjaman)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        if ($peminjaman->status !== Peminjaman::STATUS_DIAJUKAN) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak dapat disetujui.',
            ], 422);
        }

        $peminjaman->load('item');

        $overlappingQty = Peminjaman::where('item_id', $peminjaman->item_id)
            ->whereIn('status', [
                Peminjaman::STATUS_DIAJUKAN,
                Peminjaman::STATUS_DISETUJUI,
                Peminjaman::STATUS_DIPINJAM,
            ])
            ->where('tanggal_pinjam', '<=', $peminjaman->tanggal_kembali)
            ->where('tanggal_kembali', '>=', $peminjaman->tanggal_pinjam)
            ->sum('quantity');

        $availableStock = max(0, $peminjaman->item->stock - $overlappingQty);

        if ($peminjaman->quantity > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi.',
            ], 422);
        }

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DISETUJUI,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil disetujui.',
        ]);
    }

    public function reject(Peminjaman $peminjaman)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        if ($peminjaman->status !== Peminjaman::STATUS_DIAJUKAN) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman tidak dapat ditolak.',
            ], 422);
        }

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DITOLAK,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil ditolak.',
        ]);
    }
}