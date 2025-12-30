<?php
// app/Models/Peminjaman.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'item_id', 'quantity', 'tanggal_pinjam', 
        'tanggal_kembali', 'tanggal_pengembalian_aktual', 
        'status', 'denda', 'denda_dibayar'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_pengembalian_aktual' => 'date',
        'denda' => 'decimal:2',
        'denda_dibayar' => 'boolean',
    ];

    // RELATIONS
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

    // LOGIC VALIDASI STOK
    /**
     * Cek apakah stok cukup untuk peminjaman
     */
    public function stokCukup()
    {
        return $this->item->stock_tersedia >= $this->quantity;
    }

    /**
     * Get stok tersedia saat ini
     */
    public function getStokTersediaAttribute()
    {
        return $this->item->stock_tersedia;
    }

    /**
     * Validasi apakah peminjaman masih bisa diajukan
     */
    public function bisaDipinjam()
    {
        // Cek stok cukup
        if (!$this->stokCukup()) {
            return false;
        }

        // Cek apakah barang sedang dipinjam oleh user yang sama
        $existing = Peminjaman::where('user_id', $this->user_id)
            ->where('item_id', $this->item_id)
            ->whereIn('status', ['diajukan', 'disetujui'])
            ->exists();

        return !$existing;
    }

    // STATUS MANAGEMENT
    /**
     * Cek apakah peminjaman sudah melewati batas waktu
     */
    public function isTerlambat()
    {
        if ($this->status !== 'disetujui') {
            return false;
        }

        // Jika sudah dikembalikan, cek tanggal pengembalian
        if ($this->tanggal_pengembalian_aktual) {
            return $this->tanggal_pengembalian_aktual > $this->tanggal_kembali;
        }

        // Jika belum dikembalikan, cek apakah sudah lewat tanggal kembali
        return now()->greaterThan($this->tanggal_kembali);
    }

    /**
     * Hitung hari keterlambatan
     */
    // app/Models/Peminjaman.php - lanjutan dari sebelumnya

    /**
     * Hitung hari keterlambatan
     */
    public function hariKeterlambatan()
    {
        if (!$this->tanggal_pengembalian_aktual) {
            // Belum dikembalikan
            if (now()->greaterThan($this->tanggal_kembali)) {
                return now()->diffInDays($this->tanggal_kembali);
            }
            return 0;
        }

        // Sudah dikembalikan
        if ($this->tanggal_pengembalian_aktual <= $this->tanggal_kembali) {
            return 0;
        }

        return $this->tanggal_pengembalian_aktual->diffInDays($this->tanggal_kembali);
    }

    /**
     * Hitung denda otomatis (Rp 10.000 per hari)
     */
    public function hitungDenda()
    {
        $hariTerlambat = $this->hariKeterlambatan();
        $dendaPerHari = 10000; // Rp 10.000
        
        return $hariTerlambat * $dendaPerHari;
    }

    /**
     * Update denda berdasarkan keterlambatan
     */
    public function updateDenda()
    {
        $denda = $this->hitungDenda();
        
        $this->update([
            'denda' => $denda,
            // Reset status pembayaran jika denda berubah
            'denda_dibayar' => $denda > 0 ? false : $this->denda_dibayar,
        ]);
        
        return $denda;
    }

    // SCOPES
    /**
     * Scope untuk peminjaman aktif
     */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['diajukan', 'disetujui']);
    }

    /**
     * Scope untuk peminjaman terlambat
     */
    public function scopeTerlambat($query)
    {
        return $query->where('status', 'disetujui')
                    ->where('tanggal_kembali', '<', now())
                    ->whereNull('tanggal_pengembalian_aktual');
    }

    /**
     * Scope untuk peminjaman dengan denda belum dibayar
     */
    public function scopeDendaBelumDibayar($query)
    {
        return $query->where('denda', '>', 0)
                    ->where('denda_dibayar', false);
    }

    // ACCESSORS
    /**
     * Get formatted denda
     */
    public function getDendaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->denda, 0, ',', '.');
    }

    /**
     * Get status label dengan warna
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'diajukan' => ['label' => 'Diajukan', 'color' => 'warning'],
            'disetujui' => ['label' => 'Disetujui', 'color' => 'success'],
            'ditolak' => ['label' => 'Ditolak', 'color' => 'danger'],
            'dikembalikan' => ['label' => 'Dikembalikan', 'color' => 'info'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'color' => 'secondary'],
        ];

        return $labels[$this->status] ?? ['label' => ucfirst($this->status), 'color' => 'secondary'];
    }

    /**
     * Get lama peminjaman dalam hari
     */
    public function getLamaPeminjamanAttribute()
    {
        return $this->tanggal_pinjam->diffInDays($this->tanggal_kembali) + 1;
    }

    // MUTATORS
    /**
     * Set quantity dengan validasi
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = max(1, (int) $value);
    }

    /**
     * Set tanggal kembali harus setelah tanggal pinjam
     */
    public function setTanggalKembaliAttribute($value)
    {
        $tanggalPinjam = $this->tanggal_pinjam ?? now();
        
        if (Carbon::parse($value)->lessThanOrEqualTo($tanggalPinjam)) {
            throw new \InvalidArgumentException('Tanggal kembali harus setelah tanggal pinjam');
        }

        $this->attributes['tanggal_kembali'] = $value;
    }
}