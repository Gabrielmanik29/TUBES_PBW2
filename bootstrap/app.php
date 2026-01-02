<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// ✅ tambah ini
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ TANPA menghapus code awal, kita isi bagian ini dengan alias middleware
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        // kalau nanti ada alias lain, tinggal tambah di sini juga
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
