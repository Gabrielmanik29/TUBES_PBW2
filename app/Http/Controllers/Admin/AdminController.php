<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function peminjaman(Request $request)
    {
        $peminjamans = Peminjaman::with(['user', 'item.category'])
            ->latest()
            ->paginate(10);

        return view('admin.peminjaman.index', compact('peminjamans'));
    }

    public function approve(Peminjaman $peminjaman)
    {
        $peminjaman->update(['status' => 'disetujui']);
        return back()->with('success', 'Peminjaman disetujui');
    }

    public function reject(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $peminjaman->update([
            'status' => 'ditolak',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Peminjaman ditolak');
    }

    public function return(Peminjaman $peminjaman)
    {
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_pengembalian_aktual' => now(),
        ]);

        return back()->with('success', 'Barang dikembalikan');
    }
}
