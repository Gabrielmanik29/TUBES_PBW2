<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan peminjaman dengan filter dan statistik
     */
    public function index(Request $request)
    {
        // Build query dengan filter
        $query = Peminjaman::with(['user', 'item']);

        // Filter berdasarkan tanggal mulai
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }

        // Filter berdasarkan tanggal selesai
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        // Filter berdasarkan status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Ambil data dengan pagination
        $laporans = $query->latest()->paginate(15)->withQueryString();

        // ========== HITUNG STATISTIK ==========

        // 1. Total Peminjaman (dari query terfilter)
        $totalPeminjaman = (clone $query)->count();

        // 2. Total Denda (dari query terfilter)
        $totalDenda = (clone $query)->sum('denda');

        // 3. Barang Belum Kembali (status 'disetujui' / 'dipinjam')
        $barangBelumKembaliQuery = Peminjaman::whereIn('status', ['disetujui']);

        if ($request->has('start_date') && !empty($request->start_date)) {
            $barangBelumKembaliQuery->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->has('end_date') && !empty($request->end_date)) {
            $barangBelumKembaliQuery->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        $barangBelumKembali = $barangBelumKembaliQuery->count();

        // Statistik tambahan untuk cards
        $stats = [
            'total_peminjaman' => $totalPeminjaman,
            'total_denda' => $totalDenda,
            'barang_belum_kembali' => $barangBelumKembali,
            'peminjaman_selesai' => (clone $query)->where('status', 'dikembalikan')->count(),
            'peminjaman_ditolak' => (clone $query)->where('status', 'ditolak')->count(),
        ];

        return view('admin.laporan.index', compact('laporans', 'stats'));
    }

    /**
     * Export laporan ke Excel dengan filter
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');

        $filename = 'laporan-peminjaman-' . date('Y-m-d-H-i-s') . '.xlsx';

        return Excel::download(
            new LaporanExport($startDate, $endDate, $status),
            $filename
        );
    }

    /**
     * Export laporan ke PDF (placeholder - bisa dikembangkan lebih lanjut)
     */
    public function exportPdf(Request $request)
    {
        return back()->with('info', 'Fitur export PDF akan segera tersedia.');
    }
}

