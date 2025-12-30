<?php
// app/Http/Middleware/PreventRequestsDuringMaintenance.php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Tambahkan URI yang tetap bisa diakses saat maintenance mode
        // Contoh:
        // '/admin/login',
        // '/api/health',
    ];
}