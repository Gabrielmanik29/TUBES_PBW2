<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'peminjaman_id',
        'order_id',
        'amount',
        'payment_type',
        'transaction_status',
        'response_midtrans',
        'snap_token',
    ];

    protected $casts = [
        'response_midtrans' => 'array',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }
}
