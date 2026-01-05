<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peminjaman extends Model
{
    use HasFactory;

    // Konstanta denda per hari (Rp 5.000)
    public const DENDA_PER_HARI = 5000;

    // Status pembayaran denda
    public const DENDA_PENDING = 'pending';
    public const DENDA_PAID = 'paid';
    public const DENDA_FAILED = 'failed';

    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_pengembalian_aktual',
        'status',
        'denda',
        'denda_dibayar',
        'rejection_reason',
        // Fields untuk pembayaran denda via Midtrans
        'snap_token_denda',
        'denda_payment_status',
        'denda_paid_at',
        'denda_order_id',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
        'tanggal_pengembalian_aktual' => 'datetime',
        'denda' => 'integer',
        'denda_dibayar' => 'boolean',
        'denda_payment_status' => 'string',
        'denda_paid_at' => 'datetime',
        'snap_token_denda' => 'string',
        'denda_order_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Hitung denda keterlambatan
     */
    public function hitungDenda($tanggalPengembalian = null): int
    {
        $tanggalKembaliAktual = $tanggalPengembalian ?? $this->tanggal_pengembalian_aktual;

        if (!$tanggalKembaliAktual || !$this->tanggal_kembali) {
            return 0;
        }

        // Jika tanggal kembali aktual lebih dari tanggal harus kembali
        if ($tanggalKembaliAktual->gt($this->tanggal_kembali)) {
            $terlambat = $tanggalKembaliAktual->diffInDays($this->tanggal_kembali);
            return (int) ($terlambat * self::DENDA_PER_HARI);
        }

        return 0;
    }

    /**
     * Hitung keterlambatan dalam hari
     */
    public function hitungKeterlambatan($tanggalPengembalian = null): int
    {
        $tanggalKembaliAktual = $tanggalPengembalian ?? $this->tanggal_pengembalian_aktual;

        if (!$tanggalKembaliAktual || !$this->tanggal_kembali) {
            return 0;
        }

        if ($tanggalKembaliAktual->gt($this->tanggal_kembali)) {
            return (int) $tanggalKembaliAktual->diffInDays($this->tanggal_kembali);
        }

        return 0;
    }

    /**
     * Format denda dalam rupiah
     */
    public function getFormattedDendaAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->denda, 0, ',', '.');
    }

    /**
     * Cek apakah peminjaman terlambat
     */
    public function isTerlambat(): bool
    {
        if (!$this->tanggal_pengembalian_aktual || !$this->tanggal_kembali) {
            return false;
        }

        return $this->tanggal_pengembalian_aktual->gt($this->tanggal_kembali);
    }

    /**
     * Scope untuk peminjaman yang memiliki denda
     */
    public function scopeAdaDenda($query)
    {
        return $query->where('denda', '>', 0)->where('denda_dibayar', false);
    }

    /**
     * Scope untuk peminjaman yang sudah dikembalikan
     */
    public function scopeSudahDikembalikan($query)
    {
        return $query->where('status', 'dikembalikan');
    }

    // ==========================================
    // HELPER METHODS UNTUK PEMBAYARAN DENDA
    // ==========================================

    /**
     * Cek apakah pembayaran denda masih pending
     */
    public function isDendaPending(): bool
    {
        return $this->denda_payment_status === self::DENDA_PENDING && !$this->denda_dibayar;
    }

    /**
     * Cek apakah pembayaran denda sudah lunas
     */
    public function isDendaPaid(): bool
    {
        return $this->denda_dibayar || $this->denda_payment_status === self::DENDA_PAID;
    }

    /**
     * Cek apakah pembayaran denda gagal
     */
    public function isDendaFailed(): bool
    {
        return $this->denda_payment_status === self::DENDA_FAILED;
    }

    /**
     * Cek apakah pembayaran denda bisa dilakukan
     */
    public function canPayDenda(): bool
    {
        return (int) $this->denda > 0 && !$this->denda_dibayar;
    }

    /**
     * Format tanggal pembayaran denda
     */
    public function getFormattedDendaPaidAtAttribute(): ?string
    {
        return $this->denda_paid_at ? $this->denda_paid_at->format('d M Y, H:i') : null;
    }

    /**
     * Get status badge untuk pembayaran denda
     */
    public function getDendaStatusBadgeAttribute(): string
    {
        if ($this->isDendaPaid()) {
            return '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Lunas</span>';
        }

        if ($this->isDendaFailed()) {
            return '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Gagal</span>';
        }

        if ($this->isDendaPending()) {
            return '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Menunggu</span>';
        }

        return '<span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum Bayar</span>';
    }

    /**
     * Scope untuk peminjaman dengan denda belum dibayar
     */
    public function scopeDendaBelumDibayar($query)
    {
        return $query->where('denda', '>', 0)
            ->where('denda_dibayar', false);
    }

    /**
     * Scope untuk peminjaman dengan pembayaran denda sukses
     */
    public function scopeDendaSudahDibayar($query)
    {
        return $query->where('denda_dibayar', true);
    }
}

