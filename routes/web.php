<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    | Profile
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    | Items
    | (route spesifik dulu)
    */
    Route::get('/items/export/pdf', [ItemController::class, 'exportPdf'])
        ->name('items.export.pdf');

    Route::get('/items/{item}/check-availability', [PeminjamanController::class, 'checkAvailability'])
        ->name('items.check-availability');

    Route::get('/items', [ItemController::class, 'index'])
        ->name('items.index');

    Route::get('/items/{item}', [ItemController::class, 'show'])
        ->name('items.show');

    /*
    | Peminjaman (User)
    */
    Route::get('/peminjamans/history', [PeminjamanController::class, 'history'])
        ->name('peminjamans.history');

    Route::get('/peminjamans/my-borrowings', [PeminjamanController::class, 'myBorrowings'])
        ->name('peminjamans.my-borrowings');

    Route::post('/peminjamans', [PeminjamanController::class, 'store'])
        ->name('peminjamans.store');

    Route::get('/peminjamans/{peminjaman}', [PeminjamanController::class, 'show'])
        ->name('peminjamans.show');

    Route::post('/peminjamans/{peminjaman}/cancel', [PeminjamanController::class, 'cancel'])
        ->name('peminjamans.cancel');

    /*
    | Admin Routes
    */
    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/peminjamans', [PeminjamanController::class, 'adminIndex'])
                ->name('peminjamans.index');

            Route::post('/peminjamans/{peminjaman}/approve', [PeminjamanController::class, 'approve'])
                ->name('peminjamans.approve');

            Route::post('/peminjamans/{peminjaman}/reject', [PeminjamanController::class, 'reject'])
                ->name('peminjamans.reject');
        });
});

require __DIR__ . '/auth.php';
