<?php

use Illuminate\Support\Facades\Route;

// Frontend routes
Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// Include auth routes
require __DIR__.'/auth.php';

// Include admin routes
require __DIR__.'/admin.php';
