<?php
// app/Http/Controllers/PeminjamanController.php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Menyimpan pengajuan peminjaman baru
     */
    public function store(Request $request, Item $item)
    {
        // Validasi input
        $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($item) {
                    if ($value > $item->stock_tersedia) {
                        $fail("Jumlah melebihi stok tersedia. Stok tersedia: {$item->stock_tersedia}");
                    }
                }
            ],
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
        ], [
            'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh kurang dari hari ini',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam',
        ]);

        try {
            DB::beginTransaction();

            // Buat peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'quantity' => $request->quantity,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'status' => 'diajukan',
                'denda' => 0,
                'denda_dibayar' => false,
            ]);

            DB::commit();

            return redirect()->route('my.borrowings')
                ->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat peminjaman user
     */
    public function myBorrowings(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Peminjaman::with(['item.category'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $peminjamans = $query->paginate(10);
        $totalPeminjaman = Peminjaman::where('user_id', Auth::id())->count();
        $totalDenda = Peminjaman::where('user_id', Auth::id())
            ->where('denda_dibayar', false)
            ->sum('denda');

        return view('peminjamans.my-borrowings', compact(
            'peminjamans',
            'totalPeminjaman',
            'totalDenda',
            'status'
        ));
    }

    /**
     * Batalkan peminjaman yang masih diajukan
     */
    public function cancel(Peminjaman $peminjaman)
    {
        // Pastikan hanya pemilik yang bisa batalkan
        if ($peminjaman->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Hanya bisa batalkan yang statusnya 'diajukan'
        if ($peminjaman->status !== 'diajukan') {
            return redirect()->back()
                ->with('error', 'Hanya bisa membatalkan peminjaman yang masih diajukan');
        }

        $peminjaman->update(['status' => 'dibatalkan']);

        return redirect()->back()
            ->with('success', 'Peminjaman berhasil dibatalkan');
    }
}
