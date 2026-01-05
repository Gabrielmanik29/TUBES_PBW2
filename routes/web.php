<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PeminjamanController;
<<<<<<< HEAD

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
=======
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DendaPaymentController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('items.index');
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
    }

    return redirect()->route('login');
});

<<<<<<< HEAD
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
=======
Route::view('dashboard', 'dashboard')
    ->middleware(['auth'])
    ->name('dashboard');
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

<<<<<<< HEAD

=======
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // PEMINJAMAN
    Route::get('/peminjaman', [AdminController::class, 'peminjaman'])->name('admin.peminjaman');
    Route::get('/peminjaman/{peminjaman}', [AdminController::class, 'show'])->name('admin.peminjaman.show');
    Route::post('/peminjaman/{peminjaman}/approve', [AdminController::class, 'approve'])->name('admin.peminjaman.approve');
    Route::post('/peminjaman/{peminjaman}/reject', [AdminController::class, 'reject'])->name('admin.peminjaman.reject');
    Route::post('/peminjaman/{peminjaman}/return', [AdminController::class, 'return'])->name('admin.peminjaman.return');
    Route::post('/peminjaman/{peminjaman}/confirm-denda', [AdminController::class, 'confirmDendaPaid'])->name('admin.peminjaman.confirm-denda');

    // API: Calculate Denda
    Route::post('/api/calculate-denda', [AdminController::class, 'calculateDenda'])->name('admin.calculate.denda');

    // KATEGORI
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
});
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2

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
<<<<<<< HEAD
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(ItemController::class)->name('items.')->group(function () {
        // Admin item management
        Route::get('/items/create', 'create')->name('create');
        Route::post('/items', 'store')->name('store');
        Route::get('/items/{item}/edit', 'edit')->name('edit');
        Route::put('/items/{item}', 'update')->name('update');
        Route::delete('/items/{item}', 'destroy')->name('destroy');
=======
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::controller(ItemController::class)->group(function () {
        // Admin item management
        Route::get('/items', 'adminIndex')->name('admin.items.index');
        Route::get('/items/create', 'create')->name('admin.items.create');
        Route::post('/items', 'store')->name('admin.items.store');
        Route::get('/items/{item}/edit', 'edit')->name('admin.items.edit');
        Route::put('/items/{item}', 'update')->name('admin.items.update');
        Route::delete('/items/{item}', 'destroy')->name('admin.items.destroy');
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
    });
});

// ============ BORROWING ROUTES ============
<<<<<<< HEAD
// These will be handled by PeminjamanController (Chua's part)
=======
>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
Route::middleware(['auth'])->group(function () {
    // Routes untuk peminjaman user
    Route::get('/my-borrowings', [PeminjamanController::class, 'myBorrowings'])->name('my.borrowings');
    Route::post('/borrow/{item}', [PeminjamanController::class, 'store'])
        ->name('borrow.store')
        ->middleware('throttle:5,10'); // Limit 5 requests per 10 minutes
    Route::delete('/peminjaman/{peminjaman}/cancel', [PeminjamanController::class, 'cancel'])->name('peminjaman.cancel');

    Route::get('/borrow/check-availability/{item}', [PeminjamanController::class, 'checkAvailability'])
        ->name('borrow.check');
<<<<<<< HEAD
});

require __DIR__ . '/auth.php';
=======

    // Payment routes for denda
    Route::get('/payment/{peminjaman}/snap-token', [OrderController::class, 'generateSnapToken'])
        ->name('payment.snap-token');
});

// ============ DENDA PAYMENT ROUTES (MIDTRANS) ============
Route::prefix('denda')->name('denda.')->middleware(['auth'])->group(function () {
    // Halaman detail pembayaran denda
    Route::get('/{peminjaman}', [DendaPaymentController::class, 'detail'])
        ->name('detail');

    // Checkout - Generate Snap Token
    Route::post('/checkout', [DendaPaymentController::class, 'checkout'])
        ->name('checkout');

    // Callback - Webhook dari Midtrans (di-exclude dari CSRF)
    Route::post('/callback', [DendaPaymentController::class, 'callback'])
        ->name('callback');

    // Redirect URLs dari Midtrans
    Route::get('/{peminjaman}/finish', [DendaPaymentController::class, 'finish'])
        ->name('finish');
    Route::get('/{peminjaman}/unfinish', [DendaPaymentController::class, 'unfinish'])
        ->name('unfinish');
    Route::get('/{peminjaman}/failed', [DendaPaymentController::class, 'failed'])
        ->name('failed');

    // API: Get payment status
    Route::get('/api/status/{peminjaman}', [DendaPaymentController::class, 'status'])
        ->name('status');
});

// ============ MIDTRANS NOTIFICATION ROUTES ============
Route::post('/midtrans/notification', [OrderController::class, 'handleNotification'])
    ->name('midtrans.notification');

// Midtrans callback dengan validasi signature
Route::post('/payment/midtrans-callback', [OrderController::class, 'midtransCallback'])
    ->name('payment.midtrans-callback');

require __DIR__ . '/auth.php';

>>>>>>> d55b9af1f343e0e3324d653f7222d76df8c70cd2
