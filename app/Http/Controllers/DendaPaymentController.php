<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

/**
 * DendaPaymentController - Menghandle pembayaran denda peminjaman menggunakan Midtrans Snap
 * 
 * Alur pembayaran denda:
 * 1. User klik "Bayar Denda" di halaman my-borrowings
 * 2. Sistem generate Snap Token dari Midtrans
 * 3. User diarahkan ke halaman checkout untuk pembayaran
 * 4. Setelah pembayaran, Midtrans callback ke server
 * 5. Sistem update status denda_dibayar = true
 */
class DendaPaymentController extends Controller
{
    /**
     * Konstruktor - Set middleware untuk autentikasi
     */
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Menampilkan halaman detail pembayaran denda
     * 
     * @param int $peminjamanId
     * @return \Illuminate\View\View
     */
    public function detail($peminjamanId)
    {
        $peminjaman = Peminjaman::with(['item', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($peminjamanId);

        // Validasi: harus ada denda dan belum dibayar
        if ($peminjaman->denda <= 0) {
            return redirect()->route('my.borrowings')
                ->with('error', 'Peminjaman ini tidak memiliki denda.');
        }

        if ($peminjaman->denda_dibayar) {
            return redirect()->route('my.borrowings')
                ->with('info', 'Denda ini sudah dibayar.');
        }

        return view('denda.checkout', compact('peminjaman'));
    }

    /**
     * Method checkout - Membuat transaksi denda dan generate Snap Token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request)
    {
        // Validasi input
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjamans,id'
        ]);

        $peminjaman = Peminjaman::with(['item', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($request->peminjaman_id);

        // Validasi denda
        if ($peminjaman->denda <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman ini tidak memiliki denda.'
            ], 400);
        }

        if ($peminjaman->denda_dibayar) {
            return response()->json([
                'success' => false,
                'message' => 'Denda sudah pernah dibayar.'
            ], 400);
        }

        try {
            $user = Auth::user();

            // Generate unique order ID untuk denda
            $orderId = 'DENDA-' . $peminjaman->id . '-' . time();

            // ==========================================
            // PERSIAPAN DATA UNTUK MIDTRANS
            // ==========================================

            // Item details (deskripsi denda)
            $itemDetails = [
                [
                    'id' => 'DENDA-' . $peminjaman->id,
                    'price' => (int) $peminjaman->denda,
                    'quantity' => 1,
                    'name' => 'Denda Keterlambatan: ' . $peminjaman->item->name,
                ]
            ];

            // Transaction details
            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => (int) $peminjaman->denda,
            ];

            // Customer details
            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ];

            // Callbacks untuk redirect
            $callbacks = [
                'finish' => route('denda.finish', ['peminjaman_id' => $peminjaman->id]),
                'unfinish' => route('denda.unfinish', ['peminjaman_id' => $peminjaman->id]),
                'error' => route('denda.failed', ['peminjaman_id' => $peminjaman->id]),
            ];

            // ==========================================
            // GENERATE SNAP TOKEN DARI MIDTRANS
            // ==========================================
            $midtransParams = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'callbacks' => $callbacks,
            ];

            $snapToken = Snap::getSnapToken($midtransParams);

            // ==========================================
            // SIMPAN DATA KE DATABASE
            // ==========================================
            $peminjaman->update([
                'snap_token_denda' => $snapToken,
                'denda_order_id' => $orderId,
                'denda_payment_status' => 'pending',
            ]);

            // Log activity
            Log::info('Denda checkout initiated', [
                'peminjaman_id' => $peminjaman->id,
                'order_id' => $orderId,
                'denda_amount' => $peminjaman->denda,
                'user_id' => $user->id,
            ]);

            // Return response
            return response()->json([
                'success' => true,
                'message' => 'Snap Token berhasil dibuat',
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal membuat transaksi denda', [
                'peminjaman_id' => $peminjaman->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Method callback - Menangani Webhook/Notification dari Midtrans
     * 
     * Midtrans akan mengirim notifikasi ke endpoint ini
     * ketika status pembayaran berubah
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
        Log::info('Denda Payment Callback Received:', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'payment_type' => $paymentType,
        ]);

        // Cari peminjaman berdasarkan order_id
        $peminjaman = Peminjaman::where('denda_order_id', $orderId)->first();

        if (!$peminjaman) {
            Log::warning('Peminjaman not found for callback:', ['order_id' => $orderId]);
            return response()->json(['message' => 'Peminjaman not found'], 404);
        }

        // Update status berdasarkan transaction status
        switch ($transactionStatus) {
            case 'capture':
                // Capture berhasil (kartu kredit dengan 3DS)
                if ($paymentType == 'credit_card') {
                    if ($fraudStatus == 'accept') {
                        $this->markDendaPaid($peminjaman);
                    }
                }
                break;

            case 'settlement':
                // Settlement berhasil - pembayaran sukses
                $this->markDendaPaid($peminjaman);
                break;

            case 'pending':
                // Pending - menunggu pembayaran
                $peminjaman->update(['denda_payment_status' => 'pending']);
                break;

            case 'deny':
                // Deny - pembayaran ditolak
                $peminjaman->update(['denda_payment_status' => 'failed']);
                break;

            case 'expire':
                // Expire - pembayaran kadaluarsa
                $peminjaman->update(['denda_payment_status' => 'failed']);
                break;

            case 'cancel':
                // Cancel - pembayaran dibatalkan
                $peminjaman->update(['denda_payment_status' => 'failed']);
                break;

            default:
                Log::warning('Unknown transaction status:', ['status' => $transactionStatus]);
                break;
        }

        // Return response OK ke Midtrans
        return response()->json(['message' => 'Notification handled']);
    }

    /**
     * Mark denda sebagai sudah dibayar
     * 
     * @param Peminjaman $peminjaman
     */
    private function markDendaPaid(Peminjaman $peminjaman): void
    {
        $peminjaman->update([
            'denda_dibayar' => true,
            'denda_payment_status' => 'paid',
            'denda_paid_at' => now(),
        ]);

        Log::info('Denda payment confirmed', [
            'peminjaman_id' => $peminjaman->id,
            'denda_amount' => $peminjaman->denda,
            'user_id' => $peminjaman->user_id,
        ]);
    }

    /**
     * Method finish - Redirect dari Midtrans setelah pembayaran sukses
     * 
     * @param Request $request
     * @param int $peminjamanId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish(Request $request, $peminjamanId)
    {
        $peminjaman = Peminjaman::with(['item', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($peminjamanId);

        if ($peminjaman->denda_dibayar) {
            return redirect()->route('my.borrowings')
                ->with('success', 'Pembayaran denda berhasil! Terima kasih.');
        }

        return redirect()->route('my.borrowings')
            ->with('warning', 'Pembayaran berhasil diproses. Menunggu konfirmasi sistem.');
    }

    /**
     * Method unfinish - Redirect dari Midtrans jika pembayaran tidak selesai
     * 
     * @param Request $request
     * @param int $peminjamanId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unfinish(Request $request, $peminjamanId)
    {
        return redirect()->route('denda.detail', ['peminjaman_id' => $peminjamanId])
            ->with('warning', 'Pembayaran belum selesai. Silakan coba lagi.');
    }

    /**
     * Method error - Redirect dari Midtrans jika terjadi error
     * 
     * @param Request $request
     * @param int $peminjamanId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function failed(Request $request, $peminjamanId)
    {
        return redirect()->route('denda.detail', ['peminjaman_id' => $peminjamanId])
            ->with('error', 'Terjadi kesalahan pada pembayaran. Silakan coba lagi.');
    }

    /**
     * API: Get payment status untuk polling
     * 
     * @param int $peminjamanId
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($peminjamanId)
    {
        $peminjaman = Peminjaman::with(['item', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($peminjamanId);

        return response()->json([
            'success' => true,
            'denda_dibayar' => $peminjaman->denda_dibayar,
            'denda_payment_status' => $peminjaman->denda_payment_status,
            'formatted_denda' => 'Rp ' . number_format($peminjaman->denda, 0, ',', '.'),
        ]);
    }
}

