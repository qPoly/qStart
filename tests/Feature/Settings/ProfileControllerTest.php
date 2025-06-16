<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can view profile settings page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('profile.edit'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($assert) => $assert
            ->component('settings/Profile')
            ->has('mustVerifyEmail')
            ->has('status')
    );
});

test('user can update profile information', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->patch(route('profile.update'), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $response->assertRedirect(route('profile.edit'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('email verification is reset when email is changed', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    $this->actingAs($user);

    $response = $this->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => 'new@example.com',
    ]);

    $response->assertRedirect(route('profile.edit'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'new@example.com',
        'email_verified_at' => null,
    ]);
});

test('user can delete their profile', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);
    $this->actingAs($user);

    $response = $this->delete(route('profile.destroy'), [
        'password' => 'password123',
    ]);

    $response->assertRedirect('/');
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
    $this->assertGuest();
});

test('user cannot delete profile with wrong password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);
    $this->actingAs($user);

    $response = $this->delete(route('profile.destroy'), [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertDatabaseHas('users', ['id' => $user->id]);
    $this->assertAuthenticated();
});
