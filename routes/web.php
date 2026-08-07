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
Route::middleware(['maintenance'])->prefix('/')->name('frontend.')->group(function () {
    Route::view('/about', 'frontend.about')->name('about');
    Route::view('/catalog', 'frontend.catalog')->name('catalog');
    Route::view('/portfolio', 'frontend.portfolio')->name('portfolio');
    Route::view('/testimonials', 'frontend.testimonials')->name('testimonials');
    Route::view('/faq', 'frontend.faq')->name('faq');
    Route::view('/contact', 'frontend.contact')->name('contact');
    Route::view('/tracking', 'frontend.tracking')->name('tracking');
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
    // Admin Dashboard (replaces Breeze default dashboard)
    Route::view('/dashboard', 'admin.dashboard.index')->name('dashboard');

    // User order management (users can only see their own orders)
    Route::get('/orders', [App\Http\Controllers\Frontend\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:order_code}', [App\Http\Controllers\Frontend\OrderController::class, 'show'])->name('orders.show');

    // User profile
    Route::view('/profile', 'frontend.profile')->name('profile');
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
