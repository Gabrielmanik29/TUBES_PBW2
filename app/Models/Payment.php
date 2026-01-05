<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD

class Payment extends Model
{
    //
=======
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
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
}
