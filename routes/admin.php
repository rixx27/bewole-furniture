<?php

use Illuminate\Support\Facades\Route;

// Admin routes protected by authentication, email verification, and admin role
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::view('/dashboard', 'admin.dashboard.index')->name('dashboard');

    // Master Data
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->names('categories');
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->names('products');
    Route::resource('product-images', App\Http\Controllers\Admin\ProductImageController::class)->names('product-images');

    // Content
    Route::resource('hero-banners', App\Http\Controllers\Admin\HeroBannerController::class)->names('hero-banners');
    Route::patch('hero-banners/{heroBanner}/toggle-status', [App\Http\Controllers\Admin\HeroBannerController::class, 'toggleStatus'])->name('hero-banners.toggle-status');
    Route::resource('portfolios', App\Http\Controllers\Admin\PortfolioController::class)->names('portfolios');
    Route::resource('testimonials', App\Http\Controllers\Admin\TestimonialController::class)->names('testimonials');
    Route::view('/faq', 'admin.faq')->name('faq.index');

    // Transactions
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->names('orders');
    Route::resource('product-reviews', App\Http\Controllers\Admin\ProductReviewController::class)->names('product-reviews');

    // Website
    Route::resource('settings', App\Http\Controllers\Admin\SettingController::class)->names('settings');
});

