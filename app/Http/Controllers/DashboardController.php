<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    /**
     * Dashboard untuk Admin
     */
    public function adminDashboard()
    {
        // Validasi role
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya untuk admin.');
        }

        // Statistik Umum
        $stats = [
            'total_items' => Item::count(),
            'total_categories' => Category::count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_peminjaman' => Peminjaman::count(),
        ];

        // Peminjaman menunggu persetujuan
        $peminjamanMenunggu = Peminjaman::with(['user', 'item'])
            ->where('status', 'diajukan')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        // Pengembalian belum dikonfirmasi (status: dikembalikan, menunggu konfirmasi admin)
        $pengembalianMenunggu = Peminjaman::with(['user', 'item'])
            ->where('status', 'dikembalikan')
            ->orderBy('tanggal_pengembalian_aktual', 'desc')
            ->take(5)
            ->get();

        // Total denda yang belum dibayar
        $totalDendaBelumDibayar = Peminjaman::where('denda', '>', 0)
            ->where('denda_dibayar', false)
            ->sum('denda');

        // Pengembalian terlambat (sudah melewati tanggal_kembali tapi belum dikembalikan)
        $peminjamanTerlambat = Peminjaman::with(['user', 'item'])
            ->where('status', 'disetujui')
            ->where('tanggal_kembali', '<', now())
            ->count();

        // Recent activities (peminjaman terbaru)
        $recentPeminjaman = Peminjaman::with(['user', 'item'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.admin', compact(
            'stats',
            'peminjamanMenunggu',
            'pengembalianMenunggu',
            'totalDendaBelumDibayar',
            'peminjamanTerlambat',
            'recentPeminjaman'
        ));
    }

    /**
     * Dashboard untuk User/Anggota
     */
    public function userDashboard()
    {
        $userId = Auth::id();

        // Peminjaman aktif (disetujui, belum dikembalikan)
        $peminjamanAktif = Peminjaman::with(['item'])
            ->where('user_id', $userId)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->orderBy('tanggal_kembali', 'asc')
            ->get();

        // Hitung peminjaman aktif
        $countPeminjamanAktif = $peminjamanAktif->count();

        // Hitung yang sudah melewati batas waktu
        $countTerlambat = $peminjamanAktif->filter(function ($p) {
            return $p->tanggal_kembali < now();
        })->count();

        // Tagihan denda yang belum dibayar
        $tagihanDenda = Peminjaman::with(['item'])
            ->where('user_id', $userId)
            ->where('denda', '>', 0)
            ->where('denda_dibayar', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalTagihanDenda = $tagihanDenda->sum('denda');

        // Riwayat peminjaman (sudah selesai)
        $riwayatPeminjaman = Peminjaman::with(['item'])
            ->where('user_id', $userId)
            ->whereIn('status', ['dikembalikan', 'dibatalkan', 'ditolak'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Total peminjaman user
        $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();

        // Peminjaman selesai (tidak ada denda, sudah dibayar jika ada)
        $peminjamanSelesai = Peminjaman::where('user_id', $userId)
            ->whereIn('status', ['dikembalikan'])
            ->where(function ($query) {
                $query->where('denda', 0)
                    ->orWhere('denda_dibayar', true);
            })
            ->count();

        return view('dashboard.user', compact(
            'peminjamanAktif',
            'countPeminjamanAktif',
            'countTerlambat',
            'tagihanDenda',
            'totalTagihanDenda',
            'riwayatPeminjaman',
            'totalPeminjaman',
            'peminjamanSelesai'
        ));
    }
}

