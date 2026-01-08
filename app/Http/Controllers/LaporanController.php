<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Midtrans\Config;
use Midtrans\Snap;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan peminjaman dengan filter dan statistik
     */
    public function index(Request $request)
    {
        // Build query dengan filter
        $query = Peminjaman::with(['user', 'item']);

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ambil data dengan pagination
        $laporans = $query->latest()->paginate(15)->withQueryString();

        // Hitung denda real-time untuk view menggunakan method di Model Peminjaman
        $laporans->getCollection()->transform(function ($laporan) {
            $calculatedDenda = $laporan->hitungDenda();
            $laporan->denda = $calculatedDenda;
            return $laporan;
        });

        // Hitung Statistik untuk Dashboard Laporan
        $stats = [
            'total_peminjaman' => (clone $query)->count(),
            'total_denda' => $laporans->sum('denda'),
            'barang_belum_kembali' => Peminjaman::whereIn('status', ['disetujui'])->count(),
            'peminjaman_selesai' => (clone $query)->where('status', 'dikembalikan')->count(),
            'peminjaman_ditolak' => (clone $query)->where('status', 'ditolak')->count(),
        ];

        return view('admin.laporan.index', compact('laporans', 'stats'));
    }

    /**
     * Export laporan ke Excel
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
     * Generate Snap Token untuk pembayaran denda menggunakan Midtrans
     * Mengatasi Error 10023 (Unauthorized)
     */
    public function generateSnapToken($peminjamanId)
    {
        try {
            // 1. Ambil data peminjaman beserta relasi
            $peminjaman = Peminjaman::with(['user', 'item'])->findOrFail($peminjamanId);

            // 2. Hitung denda real-time dan pastikan bertipe integer
            $denda = (int) $peminjaman->hitungDenda();

            // 3. Validasi Nominal (Midtrans menolak denda Rp 0)
            if ($denda <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Denda bernilai Rp 0. Tidak ada yang perlu dibayar.'
                ], 400);
            }

            // 4. Konfigurasi Midtrans - Membaca langsung dari ENV
            // Gunakan backslash (\) untuk memastikan namespace global SDK Midtrans terpanggil
// Bagian Konfigurasi
// TULIS PERSIS SEPERTI INI (Ganti kuncinya dengan Server Key kamu):
            \Midtrans\Config::$isProduction = false;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // 5. Generate Order ID yang selalu unik setiap request (mencegah error Duplicate Order ID)
            $orderId = 'LAPORAN-DENDA-' . $peminjamanId . '-' . time();

            // 6. Susun Parameter Transaksi
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $denda,
                ],
                'item_details' => [
                    [
                        'id' => 'DENDA-' . $peminjamanId,
                        'price' => $denda,
                        'quantity' => 1,
                        'name' => 'Denda: ' . substr($peminjaman->item->name, 0, 20),
                    ]
                ],
                'customer_details' => [
                    'first_name' => $peminjaman->user->name,
                    'email' => $peminjaman->user->email,
                ],
            ];

            // 7. Request Snap Token dari Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($midtransParams);

            // 8. Update database dengan token dan order ID baru
            $peminjaman->update([
                'snap_token_denda' => $snapToken,
                'denda_order_id' => $orderId,
                'denda_payment_status' => 'pending',
                'denda' => $denda
            ]);

            // 9. Kirim token ke frontend dalam format JSON
            return response()->json(['token' => $snapToken]);

        } catch (\Exception $e) {
            // Log error secara detail di storage/logs/laravel.log
            Log::error('MIDTRANS ERROR 10023 DEBUG: ' . $e->getMessage(), [
                'peminjaman_id' => $peminjamanId,
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}