<?php

use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\StorageTestController;
use App\Http\Controllers\BookUploadController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\ReaderController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/buku', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/buku/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::get('/penulis/{user}', [PublicProfileController::class, 'author'])->name('public.author');
Route::get('/penerbit/{user}', [PublicProfileController::class, 'publisher'])->name('public.publisher');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{book}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Reader (Fase 4)
    Route::get('/baca/{book}', [ReaderController::class, 'show'])->name('reader.show');
    Route::post('/baca/{book}/progress', [ReaderController::class, 'updateProgress'])->name('reader.progress');
    Route::post('/baca/{book}/bookmarks', [ReaderController::class, 'storeBookmark'])->name('reader.bookmarks.store');
    Route::delete('/baca/{book}/bookmarks/{bookmark}', [ReaderController::class, 'destroyBookmark'])->name('reader.bookmarks.destroy');
    Route::post('/baca/{book}/highlights', [ReaderController::class, 'storeHighlight'])->name('reader.highlights.store');
    Route::delete('/baca/{book}/highlights/{highlight}', [ReaderController::class, 'destroyHighlight'])->name('reader.highlights.destroy');
    Route::patch('/reader/preferences', [ReaderController::class, 'updatePreferences'])->name('reader.preferences');

    // Transaksi & Promo Dasar (Fase 5)
    Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/keranjang', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/keranjang/{book}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/bayar', [OrderController::class, 'pay'])->name('orders.pay');

    Route::get('/pustaka', [LibraryController::class, 'index'])->name('library.index');
    Route::get('/poin', [PointController::class, 'index'])->name('points.index');

    // Author/Publisher book management
    Route::middleware('any-role:author,publisher')->prefix('author/books')->name('author.books.')->group(function () {
        Route::get('/', [BookUploadController::class, 'index'])->name('index');
        Route::get('/create', [BookUploadController::class, 'create'])->name('create');
        Route::post('/', [BookUploadController::class, 'store'])->name('store');
        Route::get('/{book}', [BookUploadController::class, 'show'])->name('show');
        Route::get('/{book}/edit', [BookUploadController::class, 'edit'])->name('edit');
        Route::patch('/{book}', [BookUploadController::class, 'update'])->name('update');
        Route::patch('/{book}/diskon', [BookUploadController::class, 'updateDiscount'])->name('discount');
        Route::delete('/{book}', [BookUploadController::class, 'destroy'])->name('destroy');
    });
});

// Fase 1 DoD: uji upload buku ke R2 — hanya Super Admin.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/storage/test', [StorageTestController::class, 'store'])->name('storage.test');
    Route::delete('/storage/test', [StorageTestController::class, 'destroy'])->name('storage.test.destroy');

    // Fase 6: kampanye flash sale (FR-4.7.8 / FR-4.13.5).
    Route::get('/flash-sale', [FlashSaleController::class, 'index'])->name('flash-sale.index');
    Route::post('/flash-sale', [FlashSaleController::class, 'store'])->name('flash-sale.store');
    Route::patch('/flash-sale/{flashSale}', [FlashSaleController::class, 'update'])->name('flash-sale.update');
    Route::post('/flash-sale/{flashSale}/activate', [FlashSaleController::class, 'activate'])->name('flash-sale.activate');
    Route::post('/flash-sale/{flashSale}/end', [FlashSaleController::class, 'end'])->name('flash-sale.end');
    Route::delete('/flash-sale/{flashSale}', [FlashSaleController::class, 'destroy'])->name('flash-sale.destroy');
});

require __DIR__.'/auth.php';

// Webhook Midtrans (server-to-server, verifikasi signature) — Fase 5.
Route::post('/midtrans/callback', [MidtransWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('midtrans.callback');

