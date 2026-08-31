<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        // Store intended URL if not already set and previous URL is valid
        if (!session()->has('url.intended')) {
            $previous = url()->previous();
            $loginUrl = route('login');
            $registerUrl = route('register');

            if ($previous && $previous !== $loginUrl && $previous !== $registerUrl) {
                session()->put('url.intended', $previous);
            }
        }

        try {
            /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
            $driver = Socialite::driver('google');
            return $driver->redirect();
        } catch (Exception $e) {
            Log::error('Google OAuth Redirect Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal terhubung ke layanan Google. Silakan coba lagi.');
        }
    }

    /**
     * Obtain the user information from Google and authenticate.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            Log::error('Google OAuth Callback Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Login dengan Google gagal atau dibatalkan. Silakan coba lagi.');
        }

        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $name = $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pengguna Google';
        $avatar = $googleUser->getAvatar();

        if (empty($email)) {
            return redirect()->route('login')->with('error', 'Akun Google Anda tidak menyediakan alamat email yang valid.');
        }

        // 1. Cari user berdasarkan google_id
        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            // Update avatar if not set
            if (!$user->avatar && $avatar) {
                $user->update(['avatar' => $avatar]);
            }
        } else {
            // 2. Cari user berdasarkan email
            $user = User::where('email', $email)->first();

            if ($user) {
                // Link Google account to existing user, keep existing roles intact
                $user->google_id = $googleId;
                if (!$user->avatar && $avatar) {
                    $user->avatar = $avatar;
                }
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }
                $user->save();
            } else {
                // 3. User baru mendaftar menggunakan Google
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'password' => Hash::make(Str::random(32)),
                    'email_verified_at' => now(),
                ]);

                // Ensure 'user' role exists and assign it
                Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
                $user->assignRole('user');
            }
        }

        // Autentikasi user dengan remember token aktif
        Auth::login($user, true);

        // Regenerate session for security
        request()->session()->regenerate();

        // Redirect intended: jika admin ke admin dashboard, jika user ke intended url / home
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('home'));
    }
}
