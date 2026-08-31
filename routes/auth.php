<?php

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

// Google OAuth Authentication
Route::middleware(['guest'])->group(function () {
    Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Settings routes (from settings.php)
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    Route::livewire('settings/security', 'pages::settings.security')
        ->middleware([
            'password.confirm',
        ])
        ->name('security.edit');
});

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit'),
        'manage' => route('security.edit'),
    ]);
})->name('well-known.passkeys');

