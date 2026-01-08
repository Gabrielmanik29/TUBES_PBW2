<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * ============================================================
     * KELOLA PEMINJAMAN - Method Index (Unified untuk semua status)
     * ============================================================
     *
     * Menampilkan SEMUA data peminjaman dengan fitur filter status.
     * Supports: diajukan, disetujui, ditolak, dikembalikan
     */
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'item.category']);

        // Filter berdasarkan status (diajukan/disetujui/dikembalikan/dll)
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan pencarian nama user atau item
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhereHas('item', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Pagination dengan 10 item per halaman
        $peminjamans = $query->paginate(10)->withQueryString();

        // Stats untuk dashboard
        $stats = [
            'total' => Peminjaman::count(),
            'diajukan' => Peminjaman::where('status', 'diajukan')->count(),
            'disetujui' => Peminjaman::where('status', 'disetujui')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
        ];

        return view('admin.peminjaman.index', compact('peminjamans', 'stats'));
    }

    /**
     * Alias untuk method index (backward compatibility)
     */
    public function peminjaman(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Menyetujui peminjaman
     */
    public function approve(Peminjaman $peminjaman)
    {
        if (!in_array($peminjaman->status, ['diajukan'])) {
            return back()->with('error', 'Hanya peminjaman dengan status "diajukan" yang dapat disetujui');
        }

        $peminjaman->update(['status' => 'disetujui']);

        Log::info('Peminjaman disetujui', [
            'peminjaman_id' => $peminjaman->id,
            'user_id' => $peminjaman->user_id,
            'item_id' => $peminjaman->item_id,
        ]);

        return back()->with('success', 'Peminjaman disetujui. User dapat mengambil barang.');
    }

    /**
     * Menolak peminjaman
     */
    public function reject(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi',
        ]);

        if (!in_array($peminjaman->status, ['diajukan'])) {
            return back()->with('error', 'Hanya peminjaman dengan status "diajukan" yang dapat ditolak');
        }

        $peminjaman->update([
            'status' => 'ditolak',
            'rejection_reason' => $request->rejection_reason,
        ]);

        Log::info('Peminjaman ditolak', [
            'peminjaman_id' => $peminjaman->id,
            'user_id' => $peminjaman->user_id,
            'alasan' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Peminjaman ditolak');
    }

    /**
     * Memproses pengembalian barang oleh admin
     * Menghitung denda keterlambatan secara otomatis
     */
    public function return(Request $request, Peminjaman $peminjaman)
    {
        // Validasi status peminjaman
        if (!in_array($peminjaman->status, ['disetujui'])) {
            return back()->with('error', 'Hanya peminjaman dengan status "disetujui" yang dapat diproses pengembaliannya');
        }

        try {
            DB::beginTransaction();

            // Tanggal pengembalian (dari request atau sekarang)
            $tanggalPengembalian = $request->has('tanggal_pengembalian')
                ? \Carbon\Carbon::parse($request->tanggal_pengembalian)
                : now();

            // Hitung keterlambatan dan denda
            $hariTerlambat = $peminjaman->hitungKeterlambatan($tanggalPengembalian);
            $denda = $peminjaman->hitungDenda($tanggalPengembalian);

            // Update peminjaman
            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_pengembalian_aktual' => $tanggalPengembalian,
                'denda' => $denda,
            ]);

            // Kembalikan stok item
            $item = $peminjaman->item;
            $item->increment('stock', $peminjaman->quantity);

            DB::commit();

            // Log activity
            Log::info('Barang dikembalikan dengan denda', [
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'item_id' => $peminjaman->item_id,
                'tanggal_kembali_seharusnya' => $peminjaman->tanggal_kembali->toDateString(),
                'tanggal_pengembalian_aktual' => $tanggalPengembalian->toDateString(),
                'hari_terlambat' => $hariTerlambat,
                'denda' => $denda,
            ]);

            $message = $denda > 0
                ? "Barang dikembalikan. Keterlambatan: {$hariTerlambat} hari. Denda: Rp " . number_format($denda, 0, ',', '.')
                : 'Barang dikembalikan tepat waktu. Tidak ada denda.';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal memproses pengembalian', [
                'peminjaman_id' => $peminjaman->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memproses pengembalian: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi pembayaran denda oleh user
     */
    public function confirmDendaPaid(Peminjaman $peminjaman)
    {
        if ($peminjaman->denda <= 0) {
            return back()->with('error', 'Peminjaman ini tidak memiliki denda');
        }

        $peminjaman->update(['denda_dibayar' => true]);

        Log::info('Denda peminjaman dikonfirmasi lunas', [
            'peminjaman_id' => $peminjaman->id,
            'denda' => $peminjaman->denda,
        ]);

        return back()->with('success', 'Pembayaran denda dikonfirmasi');
    }

    /**
     * Menampilkan detail peminjaman
     */
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'item.category']);

        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    /**
     * API: Hitung denda untuk preview
     */
    public function calculateDenda(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjamans,id',
            'tanggal_pengembalian' => 'required|date',
        ]);

        $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);

        $tanggalPengembalian = \Carbon\Carbon::parse($request->tanggal_pengembalian);
        $hariTerlambat = $peminjaman->hitungKeterlambatan($tanggalPengembalian);
        $denda = $peminjaman->hitungDenda($tanggalPengembalian);

        return response()->json([
            'success' => true,
            'hari_terlambat' => $hariTerlambat,
            'denda' => $denda,
            'formatted_denda' => 'Rp ' . number_format($denda, 0, ',', '.'),
            'tanggal_kembali_seharusnya' => $peminjaman->tanggal_kembali->format('d/m/Y'),
            'tanggal_pengembalian' => $tanggalPengembalian->format('d/m/Y'),
        ]);
    }
}

