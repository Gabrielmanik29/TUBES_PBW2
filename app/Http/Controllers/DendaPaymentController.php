<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class DendaPaymentController extends Controller
{
    /**
     * Menampilkan detail denda sebelum bayar
     */
    public function detail($peminjamanId)
    {
        $peminjaman = Peminjaman::with(['item', 'user'])
            ->where('id', $peminjamanId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ((int) $peminjaman->denda <= 0) {
            return back()->with('error', 'Tidak ada denda.');
        }

        if ($peminjaman->denda_dibayar) {
            return back()->with('info', 'Denda sudah dibayar.');
        }

        return view('denda.checkout', compact('peminjaman'));
    }

    /**
     * Proses Checkout untuk mendapatkan Snap Token Midtrans
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjamans,id'
        ]);

        $peminjaman = Peminjaman::with('item')
            ->where('id', $request->peminjaman_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ((int) $peminjaman->denda <= 0 || $peminjaman->denda_dibayar) {
            return response()->json([
                'success' => false,
                'message' => 'Denda tidak valid'
            ], 400);
        }

        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'DENDA-' . $peminjaman->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $peminjaman->denda,
            ],
            'item_details' => [[
                'id' => 'DENDA-' . $peminjaman->id,
                'price' => (int) $peminjaman->denda,
                'quantity' => 1,
                'name' => 'Denda keterlambatan ' . $peminjaman->item->name,
            ]],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'callbacks' => [
                // PERBAIKAN: Parameter disamakan dengan {peminjamanId} di web.php
                'finish_redirect_url' => route('denda.finish', [
                    'peminjamanId' => $peminjaman->id
                ]),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $peminjaman->update([
                'denda_order_id' => $orderId,
                'snap_token_denda' => $snapToken,
                'denda_payment_status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            Log::error('MIDTRANS ERROR: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook/Callback dari Midtrans
     */
    public function callback(Request $request)
    {
        $notif = new \Midtrans\Notification();

        $orderId = $notif->order_id;
        $status = $notif->transaction_status;

        $peminjaman = Peminjaman::where('denda_order_id', $orderId)->first();

        if (!$peminjaman) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (in_array($status, ['settlement', 'capture'])) {
            $peminjaman->update([
                'denda_dibayar' => true,
                'denda_payment_status' => 'paid',
                'denda_paid_at' => now(),
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Halaman Redirect setelah bayar selesai
     */
    public function finish($peminjamanId)
    {
        return redirect()
            ->route('my.borrowings')
            ->with('success', 'Denda berhasil dibayar');
    }
}