<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

<<<<<<< HEAD
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
=======
// ✅ tambah ini
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ TANPA menghapus code awal, kita isi bagian ini dengan alias middleware
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        /**
         * CSRF Protection Exclusions
         * 
         * Midtrans callback URL perlu di-exclude dari CSRF protection
         * karena Midtrans akan mengirim webhook dari server mereka,
         * bukan dari browser user.
         */
        $middleware->validateCsrfTokens(except: [
            'payment/callback', // Midtrans webhook URL
        ]);

        // kalau nanti ada alias lain, tinggal tambah di sini juga
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
