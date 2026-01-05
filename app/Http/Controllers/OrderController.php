<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Generate Snap Token untuk pembayaran denda
     */
    public function generateSnapToken(Peminjaman $peminjaman)
    {
        // Pastikan user yang login adalah pemilik peminjaman
        if ($peminjaman->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Cek apakah ada denda yang belum dibayar
        if ($peminjaman->denda <= 0 || $peminjaman->denda_dibayar) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada denda yang perlu dibayar'
            ], 400);
        }

        // Cek apakah sudah ada payment record dengan status pending
        $existingPayment = Payment::where('peminjaman_id', $peminjaman->id)
            ->where('transaction_status', 'pending')
            ->first();

        if ($existingPayment && $existingPayment->snap_token) {
            return response()->json([
                'success' => true,
                'snap_token' => $existingPayment->snap_token,
                'order_id' => $existingPayment->order_id
            ]);
        }

        try {
            // Generate unique order ID
            $orderId = 'ORDER-' . $peminjaman->id . '-' . time();

            // Hitung total amount (denda)
            $amount = $peminjaman->denda;

            // Parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $amount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'item_details' => [
                    [
                        'id' => 'DENDA-' . $peminjaman->id,
                        'price' => $amount,
                        'quantity' => 1,
                        'name' => 'Denda Peminjaman - ' . $peminjaman->item->name,
                    ]
                ],
                'callbacks' => [
                    'finish' => route('denda.finish', ['peminjaman' => $peminjaman->id]),
                    'unfinish' => route('denda.unfinish', ['peminjaman' => $peminjaman->id]),
                    'error' => route('denda.failed', ['peminjaman' => $peminjaman->id]),
                ],
            ];

            // Generate Snap Token
            $snapToken = Snap::getSnapToken($params);

            // Simpan ke database
            $payment = Payment::updateOrCreate(
                ['peminjaman_id' => $peminjaman->id],
                [
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'payment_type' => 'denda',
                    'transaction_status' => 'pending',
                    'snap_token' => $snapToken,
                    'response_midtrans' => json_encode($params),
                ]
            );

            Log::info('Snap token generated', [
                'peminjaman_id' => $peminjaman->id,
                'order_id' => $orderId,
                'amount' => $amount,
                'snap_token' => $snapToken
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'amount' => $amount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate snap token', [
                'peminjaman_id' => $peminjaman->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate token pembayaran'
            ], 500);
        }
    }

    /**
     * Handle notification dari Midtrans
     */
    public function handleNotification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        try {
            $statusCode = $notification->status_code ?? null;
            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $paymentType = $notification->payment_type ?? null;
            $fraudStatus = $notification->fraud_status ?? null;

            // Cari payment berdasarkan order_id
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::error('Payment not found for order_id: ' . $orderId);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // Update status pembayaran
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $payment->transaction_status = 'challenge';
                } else if ($fraudStatus == 'accept') {
                    $payment->transaction_status = 'paid';
                    // Update denda_dibayar di peminjaman
                    $payment->peminjaman->update(['denda_dibayar' => true]);
                }
            } else if ($transactionStatus == 'settlement') {
                $payment->transaction_status = 'paid';
                $payment->peminjaman->update(['denda_dibayar' => true]);
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $payment->transaction_status = 'failed';
            } else if ($transactionStatus == 'pending') {
                $payment->transaction_status = 'pending';
            }

            $payment->payment_type = $paymentType;
            $payment->response_midtrans = $payload;
            $payment->save();

            Log::info('Payment notification processed', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_status' => $payment->transaction_status
            ]);

            return response()->json(['message' => 'Notification processed successfully']);

        } catch (\Exception $e) {
            Log::error('Failed to process notification', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);

            return response()->json(['message' => 'Failed to process notification'], 500);
        }
    }

    /**
     * Handle Midtrans Callback dengan validasi signature
     */
    public function midtransCallback(Request $request)
    {
        try {
            // Ambil raw payload
            $payload = $request->getContent();
            $notification = json_decode($payload);

            // Validasi signature key
            $signatureKey = $this->validateSignature($notification);
            if (!$signatureKey) {
                Log::warning('Invalid signature key for Midtrans callback', [
                    'order_id' => $notification->order_id ?? 'unknown'
                ]);
                return response()->json(['message' => 'Invalid signature'], 400);
            }

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $paymentType = $notification->payment_type ?? null;
            $fraudStatus = $notification->fraud_status ?? null;

            // Cari payment berdasarkan order_id
            $payment = Payment::where('order_id', $orderId)->first();

            if (!$payment) {
                Log::error('Payment not found for callback order_id: ' . $orderId);
                return response()->json(['message' => 'Payment not found'], 404);
            }

            // Handle status pembayaran
            $newStatus = $this->mapTransactionStatus($transactionStatus, $fraudStatus);

            // Update payment status
            $payment->update([
                'transaction_status' => $newStatus,
                'payment_type' => $paymentType,
                'response_midtrans' => $payload,
            ]);

            // Jika pembayaran berhasil, update status denda di peminjaman
            if ($newStatus === 'paid') {
                $payment->peminjaman->update(['denda_dibayar' => true]);
            }

            Log::info('Midtrans callback processed', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'mapped_status' => $newStatus,
                'payment_id' => $payment->id
            ]);

            return response()->json(['message' => 'Callback processed successfully']);

        } catch (\Exception $e) {
            Log::error('Failed to process Midtrans callback', [
                'error' => $e->getMessage(),
                'payload' => $payload ?? 'no payload'
            ]);

            return response()->json(['message' => 'Failed to process callback'], 500);
        }
    }

    /**
     * Validasi signature key Midtrans
     */
    private function validateSignature($notification)
    {
        $orderId = $notification->order_id ?? '';
        $statusCode = $notification->status_code ?? '';
        $grossAmount = $notification->gross_amount ?? '';
        $serverKey = config('midtrans.server_key');
        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $signature = openssl_digest($input, 'sha512');

        return ($signature === ($notification->signature_key ?? ''));
    }

    /**
     * Map transaction status ke payment status
     */
    private function mapTransactionStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'settlement':
                return 'paid';
            case 'pending':
                return 'unpaid';
            case 'cancel':
            case 'deny':
            case 'expire':
                return 'failed';
            case 'capture':
                if ($fraudStatus === 'challenge') {
                    return 'challenge';
                } elseif ($fraudStatus === 'accept') {
                    return 'paid';
                }
                return 'failed';
            default:
                return 'unpaid';
        }
    }
}

