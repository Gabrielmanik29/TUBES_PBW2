<?php
// app/Http/Middleware/VerifyCsrfToken.php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
<<<<<<< HEAD
        // Tambahkan route yang tidak perlu CSRF protection di sini
        // Contoh: webhook dari payment gateway
        // 'payment/callback',
=======
        // Midtrans webhook callbacks - dipanggil dari server Midtrans
        'payment/callback',
        'denda/callback',
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
    ];
}