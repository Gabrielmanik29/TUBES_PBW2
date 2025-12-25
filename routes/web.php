<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PeminjamanController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// ============ ITEM ROUTES FOR USERS ============
Route::controller(ItemController::class)->name('items.')->group(function () {
    // Main item listing with filters
    Route::get('/items', 'index')->name('index');

    // Item detail page
    Route::get('/items/{item}', 'show')->name('show')->whereNumber('item');

    // AJAX endpoints
    Route::get('/api/items/search', 'searchAjax')->name('search.ajax');
    Route::get('/api/items/{item}/available-dates', 'getAvailableDates')->name('available.dates');
    Route::post('/api/items/calculate-return', 'calculateReturnDate')->name('calculate.return');

    // Export feature
    Route::get('/items/export/pdf', 'exportPdf')->name('export.pdf')->middleware('auth');
});

// ============ ADMIN ITEM ROUTES ============
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(ItemController::class)->name('items.')->group(function () {
        // Admin item management
        Route::get('/items/create', 'create')->name('create');
        Route::post('/items', 'store')->name('store');
        Route::get('/items/{item}/edit', 'edit')->name('edit');
        Route::put('/items/{item}', 'update')->name('update');
        Route::delete('/items/{item}', 'destroy')->name('destroy');
    });
});

// ============ BORROWING ROUTES ============
// These will be handled by PeminjamanController (Chua's part)
Route::middleware(['auth'])->group(function () {
    Route::post('/borrow/{item}', [PeminjamanController::class, 'store'])
        ->name('borrow.store')
        ->middleware('throttle:5,10'); // Limit 5 requests per 10 minutes

    Route::get('/borrow/check-availability/{item}', [PeminjamanController::class, 'checkAvailability'])
        ->name('borrow.check');
});

// ============ PEMINJAMAN MANAGEMENT ROUTES ============
Route::middleware(['auth'])->prefix('peminjamans')->name('peminjamans.')->group(function () {
    Route::get('/history', [PeminjamanController::class, 'history'])->name('history');
    Route::get('/my-borrowings', [PeminjamanController::class, 'myBorrowings'])->name('my-borrowings');
    Route::get('/{peminjaman}', [PeminjamanController::class, 'show'])->name('show');
    Route::post('/{peminjaman}/cancel', [PeminjamanController::class, 'cancel'])->name('cancel');
});

require __DIR__ . '/auth.php';
