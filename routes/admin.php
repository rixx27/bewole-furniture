<?php

use Illuminate\Support\Facades\Route;

// Admin routes protected by authentication, email verification, and admin role
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::view('/dashboard', 'admin.dashboard.index')->name('dashboard');

    // Master Data
    Route::get('categories', App\Livewire\Admin\Category\CategoryManager::class)->name('categories.index');
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->names('products');
    Route::resource('product-images', App\Http\Controllers\Admin\ProductImageController::class)->names('product-images');

    // Content
    Route::resource('hero-banners', App\Http\Controllers\Admin\HeroBannerController::class)->names('hero-banners');
    Route::patch('hero-banners/{heroBanner}/toggle-status', [App\Http\Controllers\Admin\HeroBannerController::class, 'toggleStatus'])->name('hero-banners.toggle-status');
    Route::resource('portfolios', App\Http\Controllers\Admin\PortfolioController::class)->names('portfolios');
    Route::resource('testimonials', App\Http\Controllers\Admin\TestimonialController::class)->names('testimonials');
    Route::resource('faqs', App\Http\Controllers\Admin\FaqController::class)->names('faqs');

    // Transactions
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->names('orders');
    Route::get('orders/{order}/invoice', [App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('orders.invoice');
    Route::patch('orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/shipping', [App\Http\Controllers\Admin\OrderController::class, 'updateShipping'])->name('orders.update-shipping');
    Route::patch('orders/{order}/payment', [App\Http\Controllers\Admin\OrderController::class, 'updatePayment'])->name('orders.update-payment');
    Route::post('orders/{order}/cancel', [App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::resource('product-reviews', App\Http\Controllers\Admin\ProductReviewController::class)->only(['index', 'show'])->names('product-reviews');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', App\Livewire\Admin\Report\OrderReport::class)->name('index');
        Route::get('/orders', App\Livewire\Admin\Report\OrderReport::class)->name('orders');
    });

    // Website
    Route::resource('settings', App\Http\Controllers\Admin\SettingController::class)->names('settings');
    Route::get('company-profile', [App\Http\Controllers\Admin\CompanyProfileController::class, 'index'])->name('company-profile.index');
});

