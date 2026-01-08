<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DendaPaymentController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MidtransCallbackController;

Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('items.index');
    }
    return redirect()->route('login');
});

// ============ TEMPORARY TEST ROUTE FOR MIDTRANS ============
Route::get('/test-midtrans', [App\Http\Controllers\TestController::class, 'testMidtrans'])->name('test.midtrans');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // ============================================================
    // KELOLA PEMINJAMAN - Unified Route
    // ============================================================
    Route::get('/peminjamans', [AdminController::class, 'index'])->name('admin.peminjamans.index');
    Route::get('/peminjaman/{peminjaman}', [AdminController::class, 'show'])->name('admin.peminjaman.show');
    Route::post('/peminjaman/{peminjaman}/approve', [AdminController::class, 'approve'])->name('admin.peminjaman.approve');
    Route::post('/peminjaman/{peminjaman}/reject', [AdminController::class, 'reject'])->name('admin.peminjaman.reject');
    Route::post('/peminjaman/{peminjaman}/return', [AdminController::class, 'return'])->name('admin.peminjaman.return');
    Route::post('/peminjaman/{peminjaman}/confirm-denda', [AdminController::class, 'confirmDendaPaid'])->name('admin.peminjaman.confirm-denda');

    // Backward compatibility alias
    Route::get('/peminjaman', [AdminController::class, 'peminjaman'])->name('admin.peminjaman');

    // API: Calculate Denda
    Route::post('/api/calculate-denda', [AdminController::class, 'calculateDenda'])->name('admin.calculate.denda');

    // KATEGORI
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // LAPORAN
    Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('admin.laporan.export.excel');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('admin.laporan.export.pdf');

    // LAPORAN - Generate Snap Token for Denda Payment (FIXED FOR 10023 ERROR)
    // Route ini sekarang mengarah ke LaporanController yang baru saja kita buat
    Route::get('/laporan/{peminjaman}/generate-snap-token', [LaporanController::class, 'generateSnapToken'])
        ->name('admin.laporan.generate-snap-token');
});

// ============ ITEM ROUTES FOR USERS ============
Route::controller(ItemController::class)->name('items.')->group(function () {
    Route::get('/items', 'index')->name('index');
    Route::get('/items/{item}', 'show')->name('show')->whereNumber('item');
    Route::get('/api/items/search', 'searchAjax')->name('search.ajax');
    Route::get('/api/items/{item}/available-dates', 'getAvailableDates')->name('available.dates');
    Route::post('/api/items/calculate-return', 'calculateReturnDate')->name('calculate.return');
    Route::get('/items/export/pdf', 'exportPdf')->name('export.pdf')->middleware('auth');
});

// ============ ADMIN ITEM ROUTES ============
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::controller(ItemController::class)->group(function () {
        Route::get('/items', 'adminIndex')->name('admin.items.index');
        Route::get('/items/create', 'create')->name('admin.items.create');
        Route::post('/items', 'store')->name('admin.items.store');
        Route::get('/items/{item}/edit', 'edit')->name('admin.items.edit');
        Route::put('/items/{item}', 'update')->name('admin.items.update');
        Route::delete('/items/{item}', 'destroy')->name('admin.items.destroy');
    });
});

// ============ BORROWING ROUTES ============
Route::middleware(['auth'])->group(function () {
    Route::get('/my-borrowings', [PeminjamanController::class, 'myBorrowings'])->name('my.borrowings');
    Route::post('/borrow/{item}', [PeminjamanController::class, 'store'])
        ->name('borrow.store')
        ->middleware('throttle:5,10'); 
    Route::delete('/peminjaman/{peminjaman}/cancel', [PeminjamanController::class, 'cancel'])->name('peminjaman.cancel');
    Route::get('/borrow/check-availability/{item}', [PeminjamanController::class, 'checkAvailability'])->name('borrow.check');

    Route::get('/payment/{peminjaman}/snap-token', [OrderController::class, 'generateSnapToken'])
        ->name('payment.snap-token');
});

// ============ DENDA PAYMENT ROUTES (MIDTRANS) ============
Route::prefix('denda')->name('denda.')->middleware(['auth'])->group(function () {
    Route::get('/{peminjamanId}', [DendaPaymentController::class, 'detail'])->name('detail');
    Route::post('/checkout', [DendaPaymentController::class, 'checkout'])->name('checkout');
    Route::post('/callback', [DendaPaymentController::class, 'callback'])
        ->name('callback')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::get('/{peminjamanId}/finish', [DendaPaymentController::class, 'finish'])->name('finish');
});

require __DIR__.'/auth.php';