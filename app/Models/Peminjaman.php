<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peminjaman extends Model
{
    use HasFactory;

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
        'denda_dibayar'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
        'tanggal_pengembalian_aktual' => 'datetime',
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

    public function hitungDenda()
    {
        if ($this->tanggal_pengembalian_aktual && $this->tanggal_pengembalian_aktual > $this->tanggal_kembali) {
            $terlambat = $this->tanggal_pengembalian_aktual->diffInDays($this->tanggal_kembali);
            $dendaPerHari = 10000; // Rp 10.000 per hari
            return $terlambat * $dendaPerHari;
        }
        return 0;
    }
}
