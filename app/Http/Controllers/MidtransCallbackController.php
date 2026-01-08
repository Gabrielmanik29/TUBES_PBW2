<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use Midtrans\Notification;

class MidtransCallbackController extends Controller
{
    public function handle(Request $request)
    {
        // Initialize variables to prevent undefined errors
        $peminjamanId = null;
        $peminjaman = null;
        $orderId = null;
        $transactionStatus = null;

        // =============================
        // KONFIGURASI MIDTRANS
        // =============================
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        try {
            // Ambil notifikasi resmi dari Midtrans
            $notification = new Notification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;

            /**
             * FORMAT ORDER ID:
             * DENDA_{peminjamanId}_{timestamp}
             * contoh: DENDA_4_1700000000
             */
            $parts = explode('_', $orderId);

            if (count($parts) < 2 || $parts[0] !== 'DENDA') {
                return response()->json(['message' => 'Order ID tidak valid'], 400);
            }

            $peminjamanId = $parts[1];

            $peminjaman = Peminjaman::find($peminjamanId);

            if (!$peminjaman) {
                return response()->json(['message' => 'Peminjaman tidak ditemukan'], 404);
            }

            // =============================
            // HANDLE STATUS TRANSAKSI
            // =============================

            if (in_array($transactionStatus, ['capture', 'settlement'])) {
                $peminjaman->update([
                    'denda_dibayar' => true,
                    'denda_payment_status' => Peminjaman::DENDA_PAID,
                    'denda_paid_at' => now(),
                ]);
            }

            if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $peminjaman->update([
                    'denda_payment_status' => Peminjaman::DENDA_FAILED,
                ]);
            }

            if ($transactionStatus === 'pending') {
                $peminjaman->update([
                    'denda_payment_status' => Peminjaman::DENDA_PENDING,
                ]);
            }

            return response()->json(['message' => 'Callback berhasil diproses']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Callback error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}