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
        // Midtrans configuration will be set directly in methods to bypass config cache
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

        // Generate unique order ID dengan timestamp
        $orderId = 'INV-' . time() . '-' . $peminjaman->id;

        // Hitung total amount (denda) dan pastikan integer
        $amount = (int) $peminjaman->denda;

        // Setup Midtrans configuration langsung dari .env
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Disable SSL verification in development mode
        if (!env('MIDTRANS_IS_PRODUCTION', false)) {
            Config::$curlOptions = [
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ];
        }

        // Parameter untuk Midtrans dengan strict type casting
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id' => 'INV-' . time() . '-' . $peminjaman->id,
                    'price' => (int) $amount,
                    'quantity' => (int) 1,
                    'name' => 'Denda Peminjaman - ' . $peminjaman->item->name,
                ]
            ],
            'callbacks' => [
                'finish' => route('denda.finish', ['peminjaman' => $peminjaman->id]),
                'unfinish' => route('denda.unfinish', ['peminjaman' => $peminjaman->id]),
                'error' => route('denda.failed', ['peminjaman' => $peminjaman->id]),
            ],
        ];

        try {
            // Generate Snap Token dengan error handling yang lebih baik
            $snapToken = Snap::getSnapToken($params);

            // Simpan ke database
            $payment = Payment::updateOrCreate(
                ['peminjaman_id' => $peminjaman->id],
                [
                    'order_id' => $orderId,
                    'amount' => (int) $amount,
                    'payment_type' => 'denda',
                    'transaction_status' => 'pending',
                    'snap_token' => $snapToken,
                    'response_midtrans' => json_encode($params),
                ]
            );

            Log::info('Snap token generated successfully', [
                'peminjaman_id' => $peminjaman->id,
                'order_id' => $orderId,
                'amount' => (int) $amount,
                'snap_token' => $snapToken
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'amount' => (int) $amount
            ]);

        } catch (\Exception $e) {
            // Log detailed error untuk debugging
            Log::error('Failed to generate snap token - Midtrans Error', [
                'peminjaman_id' => $peminjaman->id,
                'order_id' => $orderId,
                'amount' => (int) $amount,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'midtrans_params' => $params,
                'server_key_used' => env('MIDTRANS_SERVER_KEY') ? 'YES' : 'NO',
                'is_production' => env('MIDTRANS_IS_PRODUCTION', false)
            ]);

            // Return actual error message dari Midtrans
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate token pembayaran: ' . $e->getMessage(),
                'error_code' => $e->getCode(),
                'debug_info' => [
                    'order_id' => $orderId,
                    'amount' => (int) $amount,
                    'server_key_configured' => env('MIDTRANS_SERVER_KEY') ? 'YES' : 'NO'
                ]
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

