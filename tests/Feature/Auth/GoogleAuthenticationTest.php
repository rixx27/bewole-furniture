<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Ensure roles exist for test environment
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
});

test('google login redirects to google auth provider', function () {
    $response = $this->get(route('auth.google'));

    $response->assertRedirect();
    $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
});

test('new user can authenticate with google and gets assigned user role', function () {
    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('google-123456');
    $abstractUser->shouldReceive('getName')->andReturn('Google User');
    $abstractUser->shouldReceive('getNickname')->andReturn(null);
    $abstractUser->shouldReceive('getEmail')->andReturn('googleuser@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar.jpg');

    $provider = Mockery::mock(SocialiteProvider::class);
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('home'));
    $this->assertAuthenticated();

    $user = User::where('email', 'googleuser@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->google_id)->toBe('google-123456');
    expect($user->name)->toBe('Google User');
    expect($user->avatar)->toBe('https://lh3.googleusercontent.com/avatar.jpg');
    expect($user->hasRole('user'))->toBeTrue();
    expect($user->hasRole('admin'))->toBeFalse();
});

test('existing user with same email is linked to google without losing role', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'google_id' => null,
    ]);
    $admin->assignRole('admin');

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('google-admin-789');
    $abstractUser->shouldReceive('getName')->andReturn('Admin Google');
    $abstractUser->shouldReceive('getNickname')->andReturn(null);
    $abstractUser->shouldReceive('getEmail')->andReturn('admin@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/admin.jpg');

    $provider = Mockery::mock(SocialiteProvider::class);
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);

    $admin->refresh();
    expect($admin->google_id)->toBe('google-admin-789');
    expect($admin->hasRole('admin'))->toBeTrue();
});

test('existing user with google_id can log in directly', function () {
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'google_id' => 'google-existing-000',
    ]);
    $user->assignRole('user');

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('google-existing-000');
    $abstractUser->shouldReceive('getName')->andReturn('Existing User');
    $abstractUser->shouldReceive('getNickname')->andReturn(null);
    $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn(null);

    $provider = Mockery::mock(SocialiteProvider::class);
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($user);
});

test('google login callback handles exceptions gracefully', function () {
    $provider = Mockery::mock(SocialiteProvider::class);
    $provider->shouldReceive('user')->andThrow(new Exception('Invalid OAuth State'));

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();
});
