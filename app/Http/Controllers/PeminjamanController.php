<?php
// app/Http/Controllers/PeminjamanController.php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    /**
     * Display a listing of the user's borrowings.
     */
    public function myBorrowings()
    {
        // Pastikan user login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Ambil data peminjaman user saat ini dengan eager loading
        $peminjamans = Peminjaman::with(['item.category'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Hitung statistik untuk user
        $stats = [
            'total' => Peminjaman::where('user_id', Auth::id())->count(),
            'active' => Peminjaman::where('user_id', Auth::id())
                ->whereIn('status', ['diajukan', 'disetujui'])
                ->count(),
            'overdue' => Peminjaman::where('user_id', Auth::id())
                ->where('status', 'disetujui')
                ->where('tanggal_kembali', '<', now())
                ->whereNull('tanggal_pengembalian_aktual')
                ->count(),
        ];

        return view('peminjaman.my-borrowings', compact('peminjamans', 'stats'));
    }

    /**
     * Store a newly created borrowing request.
     */
    public function store(Request $request, $itemId) // UBAH: Item $item jadi $itemId
    {
        // Validasi item ID
        $item = Item::findOrFail($itemId);

        // Validasi input form
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($item) {
                    if ($value > $item->stock_tersedia) {
                        $fail('Jumlah melebihi stok tersedia. Stok tersedia: ' . $item->stock_tersedia);
                    }
                },
            ],
            'tanggal_pinjam' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'tanggal_kembali' => [
                'required',
                'date',
                'after:tanggal_pinjam',
                function ($attribute, $value, $fail) use ($request) {
                    $tanggalPinjam = Carbon::parse($request->tanggal_pinjam);
                    $tanggalKembali = Carbon::parse($value);
                    $lamaPinjam = $tanggalPinjam->diffInDays($tanggalKembali);
                    
                    if ($lamaPinjam > 7) {
                        $fail('Maksimal peminjaman adalah 7 hari');
                    }
                    
                    if ($lamaPinjam < 1) {
                        $fail('Minimal peminjaman adalah 1 hari');
                    }
                },
            ],
        ], [
            'quantity.required' => 'Jumlah harus diisi',
            'quantity.integer' => 'Jumlah harus angka',
            'quantity.min' => 'Jumlah minimal 1',
            'tanggal_pinjam.required' => 'Tanggal pinjam harus diisi',
            'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam minimal hari ini',
            'tanggal_kembali.required' => 'Tanggal kembali harus diisi',
            'tanggal_kembali.after' => 'Tanggal kembali harus setelah tanggal pinjam',
        ]);

        // Cek apakah user sudah meminjam barang yang sama yang belum dikembalikan
        $existingBorrowing = Peminjaman::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->first();

        if ($existingBorrowing) {
            return redirect()->back()
                ->withErrors(['item' => 'Anda sudah meminjam barang ini dan belum mengembalikan'])
                ->withInput();
        }

        // Mulai transaction untuk consistency data
        DB::beginTransaction();
        try {
            // Buat record peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'quantity' => $validated['quantity'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'status' => 'diajukan',
                'denda' => 0,
                'denda_dibayar' => false,
            ]);

            DB::commit();

            return redirect()->route('my.borrowings')
                ->with('success', 'Peminjaman berhasil diajukan. Menunggu persetujuan admin.')
                ->with('peminjaman_id', $peminjaman->id);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Cancel a borrowing request (only if status is 'diajukan')
     */
    public function cancel($peminjamanId) // UBAH: Peminjaman $peminjaman jadi $peminjamanId
    {
        $peminjaman = Peminjaman::findOrFail($peminjamanId);

        // Authorization: hanya user yang membuat peminjaman yang bisa cancel
        if ($peminjaman->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Hanya bisa cancel jika status masih 'diajukan'
        if ($peminjaman->status !== 'diajukan') {
            return redirect()->route('my.borrowings')
                ->with('error', 'Tidak bisa membatalkan peminjaman yang sudah ' . $peminjaman->status);
        }

        $peminjaman->update(['status' => 'dibatalkan']);

        return redirect()->route('my.borrowings')
            ->with('success', 'Peminjaman berhasil dibatalkan');
    }
}