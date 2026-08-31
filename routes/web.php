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

// Public frontend routes
Route::middleware(['maintenance'])->group(function () {
    Route::prefix('/')->name('frontend.')->group(function () {
        Route::view('/tentang-kami', 'frontend.about')->name('about');
        Route::view('/contact', 'frontend.contact')->name('contact');
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
