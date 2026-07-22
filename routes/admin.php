<?php

use Illuminate\Support\Facades\Route;

// Admin routes will be added here
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Placeholder for admin routes
});

