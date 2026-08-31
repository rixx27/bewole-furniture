<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public route /contact returns 200 and displays contact details', function () {
    $response = $this->get('/contact');
    $response->assertOk();
    $response->assertSee('Hubungi');
    $response->assertSee('Lokasi Workshop');
    $response->assertSee('WhatsApp', escape: false);
});

test('public route /tentang-kami returns 200', function () {
    $response = $this->get('/tentang-kami');
    $response->assertOk();
    $response->assertSee('Tentang Kami');
});

test('public route /tracking returns 200', function () {
    $response = $this->get('/tracking');
    $response->assertOk();
    $response->assertSee('Lacak Pesanan');
});

test('user route /profile redirects guests to login', function () {
    $response = $this->get('/profile');
    $response->assertRedirect(route('login'));
});

test('user route /profile redirects authenticated verified user to profile.edit', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/profile');
    $response->assertRedirect(route('profile.edit'));
});
