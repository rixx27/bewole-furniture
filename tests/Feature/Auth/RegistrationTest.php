<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register with 8 lowercase letters without uppercase or symbols', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password', // 8 lowercase characters, no uppercase, no symbols
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('home', absolute: false));

    $this->assertAuthenticated();
});

test('new users can register with uppercase and symbols if desired', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'Secret@2026!', // uppercase & symbols optionally allowed
        'password_confirmation' => 'Secret@2026!',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('home', absolute: false));

    $this->assertAuthenticated();
});

test('registration fails when password is less than 8 characters', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Short Pwd User',
        'email' => 'short@example.com',
        'password' => 'short12', // 7 characters
        'password_confirmation' => 'short12',
    ]);

    $response->assertSessionHasErrors(['password']);
    $this->assertGuest();
});