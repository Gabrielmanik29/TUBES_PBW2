<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

/**
 * PaymentController - Menghandle pembayaran menggunakan Midtrans Snap
 * 
 * Method yang tersedia:
 * - checkout(): Membuat transaksi baru dan generate Snap Token
 * - callback(): Menangani webhook/notification dari Midtrans
 * - finish(), unfinish(), error(): Menangani redirect dari Midtrans
 */
class PaymentController extends Controller
{
    /**
     * Konstruktor - Set middleware untuk autentikasi
     * Semua method dalam controller ini memerlukan login kecuali callback
     */
    public function __construct()
    {
        // Midtrans configuration will be set directly in methods to bypass config cache
    }

    /**
     * Method checkout - Membuat transaksi baru dan generate Snap Token
     * 
     * Alur:
     * 1. Validasi input (order_id)
     * 2. Ambil data order berdasarkan order_id
     * 3. Generate Snap Token dari Midtrans
     * 4. Simpan snap_token ke database
     * 5. Return snap_token ke frontend
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        // Validasi input
        $request->validate([
            'order_id' => 'required|exists:orders,order_id'
        ]);

        // Get user yang sedang login
        $user = Auth::user();

        // Ambil order berdasarkan order_id (bukan id)
        $order = Order::where('order_id', $request->order_id)
            ->where('user_id', $user->id)
            ->with('orderItems.product')
            ->first();

        // Jika order tidak ditemukan atau bukan milik user
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        // Cek apakah order sudah dibayar
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Order sudah pernah dibayar'
            ], 400);
        }

        try {
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

            // ==========================================
            // PERSIAPAN DATA UNTUK MIDTRANS
            // ==========================================

            // 1. Siapkan item details dari order items
            $itemDetails = [];
            foreach ($order->orderItems as $item) {
                $itemDetails[] = [
                    'id' => $item->product_id ?? $item->id,
                    'price' => (int) $item->price,
                    'quantity' => (int) $item->quantity,
                    'name' => $item->product->name ?? 'Product #' . $item->id,
                ];
            }

            // 2. Siapkan data transaksi
            $transactionDetails = [
                'order_id' => $order->order_id,
                'gross_amount' => (int) $order->total_amount,
            ];

            // 3. Siapkan data customer
            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $request->phone ?? '',
            ];

            // 4. Buat array lengkap untuk Snap API
            $midtransParams = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'callbacks' => [
                    // Redirect setelah pembayaran selesai
                    'finish' => route('payment.finish'),
                    // Redirect jika pembayaran tidak selesai
                    'unfinish' => route('payment.unfinish'),
                    // Redirect jika terjadi error
                    'error' => route('payment.error'),
                ],
            ];

            // ==========================================
            // GENERATE SNAP TOKEN DARI MIDTRANS
            // ==========================================
            $snapToken = Snap::getSnapToken($midtransParams);

            // ==========================================
            // SIMPAN SNAP TOKEN KE DATABASE
            // ==========================================
            $order->update([
                'snap_token' => $snapToken,
                'payment_status' => 'unpaid',
            ]);

            // Return response sukses dengan snap_token
            return response()->json([
                'success' => true,
                'message' => 'Snap Token berhasil dibuat',
                'snap_token' => $snapToken,
                'order_id' => $order->order_id,
            ]);

        } catch (\Exception $e) {
            // Log detailed error untuk debugging
            Log::error('Failed to create payment transaction - Midtrans Error', [
                'order_id' => $order->order_id,
                'user_id' => $user->id,
                'total_amount' => (int) $order->total_amount,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'midtrans_params' => isset($midtransParams) ? $midtransParams : 'N/A',
                'server_key_used' => env('MIDTRANS_SERVER_KEY') ? 'YES' : 'NO',
                'is_production' => env('MIDTRANS_IS_PRODUCTION', false)
            ]);

            // Return actual error message dari Midtrans
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage(),
                'error_code' => $e->getCode(),
                'debug_info' => [
                    'order_id' => $order->order_id,
                    'amount' => (int) $order->total_amount,
                    'server_key_configured' => env('MIDTRANS_SERVER_KEY') ? 'YES' : 'NO',
                    'is_production' => env('MIDTRANS_IS_PRODUCTION', false)
                ]
            ], 500);
        }
    }

    /**
     * Method callback - Menangani Webhook/Notification dari Midtrans
     * 
     * Midtrans akan mengirim notifikasi ke endpoint ini
     * ketika status pembayaran berubah (success, pending, failed, dll)
     * 
     * Catatan: Route ini harus di-exclude dari CSRF protection
     * karena dipanggil oleh Midtrans, bukan oleh browser user
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        // Ambil notifikasi dari request
        $notif = new Notification();

        // Ambil data dari notifikasi
        $transactionStatus = $notif->transaction_status;
        $orderId = $notif->order_id;
        $paymentType = $notif->payment_type;
        $fraudStatus = $notif->fraud_status ?? null;

        // Log untuk debugging
        \Log::info('Midtrans Callback Received:', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'payment_type' => $paymentType,
        ]);

        // Cari order berdasarkan order_id
        $order = Order::where('order_id', $orderId)->first();

        if (!$order) {
            // Order tidak ditemukan
            \Log::warning('Order not found for callback:', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update status pembayaran berdasarkan transaction status
        switch ($transactionStatus) {
            case 'capture':
                // Capture berhasil (kartu kredit dengan 3DS)
                if ($paymentType == 'credit_card') {
                    if ($fraudStatus == 'accept') {
                        $this->updatePaymentStatus($order, 'paid');
                    }
                }
                break;

            case 'settlement':
                // Settlement berhasil - pembayaran sukses
                $this->updatePaymentStatus($order, 'paid');
                break;

            case 'pending':
                // Pending - menunggu pembayaran
                $this->updatePaymentStatus($order, 'unpaid');
                break;

            case 'deny':
                // Deny - pembayaran ditolak
                $this->updatePaymentStatus($order, 'failed');
                break;

            case 'expire':
                // Expire - pembayaran kadaluarsa
                $this->updatePaymentStatus($order, 'failed');
                break;

            case 'cancel':
                // Cancel - pembayaran dibatalkan
                $this->updatePaymentStatus($order, 'failed');
                break;

            default:
                // Status tidak dikenal
                \Log::warning('Unknown transaction status:', ['status' => $transactionStatus]);
                break;
        }

        // Return response OK ke Midtrans
        return response()->json(['message' => 'Notification handled']);
    }

    /**
     * Helper method untuk update status pembayaran
     * 
     * @param Order $order
     * @param string $status
     */
    private function updatePaymentStatus(Order $order, string $status): void
    {
        $updateData = ['payment_status' => $status];

        // Jika status adalah 'paid', catat waktu pembayaran
        if ($status === 'paid') {
            $updateData['paid_at'] = now();
        }

        $order->update($updateData);

        // Log update
        \Log::info('Payment status updated:', [
            'order_id' => $order->order_id,
            'new_status' => $status,
        ]);
    }

    /**
     * Method finish - Redirect dari Midtrans setelah pembayaran sukses
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');

        if ($orderId) {
            $order = Order::where('order_id', $orderId)->first();
            if ($order) {
                return redirect()->route('orders.show', $order->id)
                    ->with('success', 'Pembayaran berhasil! Terima kasih.');
            }
        }

        return redirect()->route('dashboard')
            ->with('success', 'Pembayaran berhasil!');
    }

    /**
     * Method unfinish - Redirect dari Midtrans jika pembayaran tidak selesai
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unfinish(Request $request)
    {
        $orderId = $request->get('order_id');

        if ($orderId) {
            $order = Order::where('order_id', $orderId)->first();
            if ($order) {
                return redirect()->route('orders.show', $order->id)
                    ->with('warning', 'Pembayaran belum selesai. Silakan coba lagi.');
            }
        }

        return redirect()->route('dashboard')
            ->with('warning', 'Pembayaran belum selesai.');
    }

    /**
     * Method error - Redirect dari Midtrans jika terjadi error
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function error(Request $request)
    {
        $orderId = $request->get('order_id');

        if ($orderId) {
            $order = Order::where('order_id', $orderId)->first();
            if ($order) {
                return redirect()->route('orders.show', $order->id)
                    ->with('error', 'Terjadi kesalahan pada pembayaran. Silakan coba lagi.');
            }
        }

        return redirect()->route('dashboard')
            ->with('error', 'Terjadi kesalahan pada pembayaran.');
    }
}

