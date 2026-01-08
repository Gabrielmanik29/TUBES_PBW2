<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    // =========================
    // KONSTANTA DENDA
    // =========================
    public const DENDA_PER_HARI = 5000;

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

    // =========================
    // RELASI
    // =========================
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

    // =========================
    // LOGIC DENDA
    // =========================
    public function hitungKeterlambatan($tanggalPengembalian = null): int
    {
        $tanggalPengembalian = $tanggalPengembalian
            ?? $this->tanggal_pengembalian_aktual
            ?? now();

        if (!$this->tanggal_kembali) {
            return 0;
        }

        $tanggalPengembalian = Carbon::parse($tanggalPengembalian)->startOfDay();
        $tanggalKembali = Carbon::parse($this->tanggal_kembali)->startOfDay();

        if ($tanggalPengembalian->isAfter($tanggalKembali)) {
            return $tanggalKembali->diffInDays($tanggalPengembalian);
        }

        return 0;
    }

    public function hitungDenda($tanggalPengembalian = null): int
    {
        return $this->hitungKeterlambatan($tanggalPengembalian) * self::DENDA_PER_HARI;
    }

    public function isTerlambat(): bool
    {
        return $this->hitungKeterlambatan() > 0;
    }

    public function getFormattedDendaAttribute(): string
    {
        return 'Rp ' . number_format((int) $this->denda, 0, ',', '.');
    }

    // =========================
    // QUERY SCOPE
    // =========================
    public function scopeAdaDenda($query)
    {
        return $query->where('denda', '>', 0)
            ->where('denda_dibayar', false);
    }

    public function scopeSudahDikembalikan($query)
    {
        return $query->where('status', 'dikembalikan');
    }

    // =========================
    // STATUS DENDA
    // =========================
    public function isDendaPending(): bool
    {
        return $this->denda_payment_status === self::DENDA_PENDING && !$this->denda_dibayar;
    }

    public function isDendaPaid(): bool
    {
        return $this->denda_dibayar || $this->denda_payment_status === self::DENDA_PAID;
    }

    public function isDendaFailed(): bool
    {
        return $this->denda_payment_status === self::DENDA_FAILED;
    }

    public function canPayDenda(): bool
    {
        return (int) $this->denda > 0 && !$this->denda_dibayar;
    }

    public function getFormattedDendaPaidAtAttribute(): ?string
    {
        return $this->denda_paid_at
            ? $this->denda_paid_at->format('d M Y, H:i')
            : null;
    }

    public function getDendaStatusBadgeAttribute(): string
    {
        if ($this->isDendaPaid()) {
            return '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Lunas</span>';
        }

        if ($this->isDendaFailed()) {
            return '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Gagal</span>';
        }

        if ($this->isDendaPending()) {
            return '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Menunggu</span>';
        }

        return '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Belum Bayar</span>';
    }

    // =========================
    // MIDTRANS SNAP (FIX FINAL)
    // =========================
    public static function payWithMidtrans(int $peminjamanId): string
    {
        $peminjaman = self::with(['user', 'item'])->findOrFail($peminjamanId);

        if ($peminjaman->denda <= 0) {
            throw new \Exception('Tidak ada denda.');
        }

        if ($peminjaman->denda_dibayar) {
            throw new \Exception('Denda sudah dibayar.');
        }

        // Config Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // ORDER ID AMAN (TANPA SLASH)
        $orderId = 'DENDA_' . $peminjaman->id . '_' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $peminjaman->denda,
            ],
            'item_details' => [
                [
                    'id' => 'DENDA_' . $peminjaman->id,
                    'price' => (int) $peminjaman->denda,
                    'quantity' => 1,
                    'name' => 'Denda Keterlambatan - ' . $peminjaman->item->name,
                ],
            ],
            'customer_details' => [
                'first_name' => $peminjaman->user->name,
                'email' => $peminjaman->user->email,
                'phone' => $peminjaman->user->phone ?? '',
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $peminjaman->update([
            'snap_token_denda' => $snapToken,
            'denda_order_id' => $orderId,
            'denda_payment_status' => self::DENDA_PENDING,
        ]);

        return $snapToken;
    }
}