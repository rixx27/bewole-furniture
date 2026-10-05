<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
});

// CASE 1: Password saat ini benar
test('case 1: password can be updated when current password is correct', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('current_password', 'old-password-123')
        ->set('password', 'new-password-456')
        ->set('password_confirmation', 'new-password-456')
        ->call('updatePassword');

    $response->assertHasNoErrors()
        ->assertSet('passwordSuccessMessage', 'Kata sandi berhasil diperbarui.');

    expect(Hash::check('new-password-456', $user->fresh()->password))->toBeTrue();
});

// CASE 2: Password saat ini salah
test('case 2: password update fails and password is unchanged when current password is wrong', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password-456')
        ->set('password_confirmation', 'new-password-456')
        ->call('updatePassword');

    $response->assertHasErrors(['current_password'])
        ->assertSee('Kata sandi saat ini tidak sesuai.');

    expect(Hash::check('old-password-123', $user->fresh()->password))->toBeTrue();
});

// CASE 3: Password baru kurang dari minimum (8 karakter)
test('case 3: password update fails when new password is less than 8 characters', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('current_password', 'old-password-123')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('updatePassword');

    $response->assertHasErrors(['password']);
    expect(Hash::check('old-password-123', $user->fresh()->password))->toBeTrue();
});

// CASE 4: Password confirmation berbeda
test('case 4: password update fails when password confirmation does not match', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('current_password', 'old-password-123')
        ->set('password', 'new-password-456')
        ->set('password_confirmation', 'different-password-789')
        ->call('updatePassword');

    $response->assertHasErrors(['password'])
        ->assertSee('Konfirmasi kata sandi tidak cocok.');

    expect(Hash::check('old-password-123', $user->fresh()->password))->toBeTrue();
});

// CASE 4b: Password baru tidak boleh sama dengan password saat ini
test('case 4b: password update fails when new password is same as current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('same-password-123'),
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->set('current_password', 'same-password-123')
        ->set('password', 'same-password-123')
        ->set('password_confirmation', 'same-password-123')
        ->call('updatePassword');

    $response->assertHasErrors(['password'])
        ->assertSee('Kata sandi baru tidak boleh sama dengan kata sandi saat ini.');
});

// CASE 5: User mencoba mengubah profile user lain / Authorization
test('case 5: guests cannot access profile and users can only update their own profile', function () {
    // Guest access redirected to login
    $this->get('/profile')->assertRedirect('/login');
    $this->get(route('profile.edit'))->assertRedirect('/login');

    $userA = User::factory()->create(['name' => 'User A', 'phone' => '081111111111']);
    $userB = User::factory()->create(['name' => 'User B', 'phone' => '082222222222']);

    $this->actingAs($userA);

    Livewire::test('pages::settings.profile')
        ->set('name', 'User A Updated')
        ->set('phone', '081999999999')
        ->call('updateProfileInformation');

    expect($userA->fresh()->name)->toBe('User A Updated');
    expect($userA->fresh()->phone)->toBe('081999999999');

    // Ensure User B was NOT modified
    expect($userB->fresh()->name)->toBe('User B');
    expect($userB->fresh()->phone)->toBe('082222222222');
});

// CASE 6: User tidak memiliki avatar -> Default avatar tampil
test('case 6: default initial avatar is used when user has no avatar', function () {
    $user = User::factory()->create([
        'name' => 'Krisna Furniture',
        'avatar' => null,
    ]);

    $this->actingAs($user);

    expect($user->avatarUrl())->toBeNull();
    expect($user->initials())->toBe('KF');

    $response = $this->get(route('profile.edit'));
    $response->assertOk();
    $response->assertSee('KF');
});

// CASE 7: User menghapus avatar -> Default avatar kembali
test('case 7: user can delete avatar and revert to default avatar', function () {
    Storage::disk('public')->put('avatars/test-avatar.jpg', 'avatar-content');

    $user = User::factory()->create([
        'name' => 'Krisna Furniture',
        'avatar' => 'avatars/test-avatar.jpg',
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->assertSet('avatar', 'avatars/test-avatar.jpg')
        ->call('deleteAvatar');

    $response->assertHasNoErrors()
        ->assertSet('avatar', null)
        ->assertSet('profileSuccessMessage', 'Foto profil berhasil dihapus.');

    $user->refresh();
    expect($user->avatar)->toBeNull();
    expect($user->avatarUrl())->toBeNull();
    expect(Storage::disk('public')->exists('avatars/test-avatar.jpg'))->toBeFalse();
});

// CASE 8: Google account tanpa local password -> Tidak merusak authentication
test('case 8: google account without local password can set password without current password', function () {
    $user = User::factory()->create([
        'google_id' => 'google-oauth-unique-12345',
        'password' => null, // Account created via OAuth without local password
    ]);

    $this->actingAs($user);

    $response = Livewire::test('pages::settings.profile')
        ->assertSet('google_id', 'google-oauth-unique-12345')
        ->assertSet('hasPassword', false)
        ->assertSet('loginMethod', 'Google')
        // Can set a new password directly
        ->set('password', 'google-user-new-pwd-123')
        ->set('password_confirmation', 'google-user-new-pwd-123')
        ->call('updatePassword');

    $response->assertHasNoErrors()
        ->assertSet('passwordSuccessMessage', 'Kata sandi berhasil diperbarui.');

    $user->refresh();
    expect($user->google_id)->toBe('google-oauth-unique-12345');
    expect(Hash::check('google-user-new-pwd-123', $user->password))->toBeTrue();
});

// Additional test: Avatar upload and WhatsApp update
test('user can upload a new avatar and update whatsapp phone number', function () {
    $user = User::factory()->create([
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
        'avatar' => null,
    ]);

    $this->actingAs($user);

    $fakeImage = UploadedFile::fake()->image('custom_avatar.jpg', 200, 200);

    $response = Livewire::test('pages::settings.profile')
        ->set('newAvatar', $fakeImage)
        ->set('name', 'Budi Santoso Perubahan')
        ->set('phone', '089876543210')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors()
        ->assertSet('profileSuccessMessage', 'Profil berhasil diperbarui.');

    $user->refresh();
    expect($user->name)->toBe('Budi Santoso Perubahan');
    expect($user->phone)->toBe('089876543210');
    expect($user->avatar)->not->toBeNull();
    expect(Storage::disk('public')->exists($user->avatar))->toBeTrue();
});
