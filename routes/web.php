<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes (Public)
|--------------------------------------------------------------------------
*/

Route::get('/', [App\Http\Controllers\Frontend\HomeController::class, 'index'])
    ->name('home')
    ->middleware('maintenance');

// SEO Sitemap
Route::get('/sitemap.xml', [App\Http\Controllers\Frontend\SitemapController::class, 'index'])->name('sitemap');


// Public frontend routes
Route::middleware(['maintenance'])->group(function () {
    Route::prefix('/')->name('frontend.')->group(function () {
        Route::view('/tentang-kami', 'frontend.about')->name('about');
        Route::view('/contact', 'frontend.contact')->name('contact');
        Route::view('/privacy-policy', 'frontend.privacy')->name('privacy');
        Route::get('/tracking/{order_code?}', function ($order_code = null) {
            return view('frontend.tracking', ['order_code' => $order_code]);
        })->name('tracking');
    });

    // Product catalog — public
    Route::get('/produk', [App\Http\Controllers\Frontend\ProductController::class, 'index'])->name('products.index');
    Route::get('/catalog', fn () => redirect()->route('products.index'))->name('frontend.catalog');
    Route::get('/produk/{product:slug}', [App\Http\Controllers\Frontend\ProductController::class, 'show'])->name('products.show');
    
    // Search
    Route::view('/search', 'frontend.pages.products')->name('search.index');
    Route::get('/api/search/suggest', [App\Http\Controllers\Api\SearchController::class, 'suggest'])->name('api.search.suggest');

    // Cart — public (session-based, no login required to view/add)
    Route::view('/keranjang', 'frontend.pages.cart')->name('cart.index');

    // Custom Furniture
    Route::post('/custom-furniture/upload', [App\Http\Controllers\Frontend\CustomFurnitureController::class, 'uploadDesign'])
        ->name('custom-furniture.upload');
    Route::post('/custom-furniture/store', [App\Http\Controllers\Frontend\CustomFurnitureController::class, 'store'])
        ->name('custom-furniture.store');

    // Unduh Foto Dokumentasi Progres Pesanan
    Route::get('/orders/progress-photo/{history}/download', function (\App\Models\OrderStatusHistory $history) {
        if (!$history->photo || !\Illuminate\Support\Facades\Storage::disk('public')->exists($history->photo)) {
            abort(404, 'Foto dokumentasi progres tidak ditemukan.');
        }

        $orderCode = $history->order?->order_code ?? 'pesanan';
        $statusSlug = \Illuminate\Support\Str::slug($history->status_label);
        $filename = "dokumentasi-{$orderCode}-{$statusSlug}.webp";

        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $history->photo,
            $filename,
            ['Content-Type' => 'image/webp']
        );
    })->name('orders.progress-photo.download');

    // Lihat Penuh Foto Dokumentasi Progres Pesanan (Branded Viewer)
    Route::get('/orders/progress-photo/{history}/view', function (\App\Models\OrderStatusHistory $history) {
        if (!$history->photo || !\Illuminate\Support\Facades\Storage::disk('public')->exists($history->photo)) {
            abort(404, 'Foto dokumentasi progres tidak ditemukan.');
        }

        return view('frontend.orders.photo-view', [
            'history' => $history,
            'order' => $history->order,
        ]);
    })->name('orders.progress-photo.view');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
|
| Authenticated users can access frontend pages and their own orders/profile.
|
*/

Route::middleware(['auth', 'verified', 'maintenance'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->hasRole('admin')) {
            return view('admin.dashboard.index');
        }

        return redirect()->route('orders.index');
    })->name('dashboard');

    // User order management (users can only see their own orders)
    Route::get('/orders', [App\Http\Controllers\Frontend\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:order_code}', [App\Http\Controllers\Frontend\OrderController::class, 'show'])->name('orders.show');

    // Checkout (requires login)
    Route::view('/checkout', 'frontend.pages.checkout')->name('checkout.index');

    // User profile
    Route::get('/profile', fn () => redirect()->route('profile.edit'))->name('profile');
});

/*
|--------------------------------------------------------------------------
| Auth & Admin Routes
|--------------------------------------------------------------------------
*/

// Include auth routes (login, register, password reset, etc.)
require __DIR__.'/auth.php';

// Include admin routes (protected by admin role middleware)
require __DIR__.'/admin.php';
