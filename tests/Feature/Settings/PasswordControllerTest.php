<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can view password settings page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('password.edit'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($assert) => $assert
            ->component('settings/Password')
    );
});

test('user can update their password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword123'),
    ]);
    $this->actingAs($user);

    $response = $this->put(route('password.update'), [
        'current_password' => 'oldpassword123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();

    $updatedUser = User::find($user->id);
    expect(Hash::check('newpassword123', $updatedUser->password))->toBeTrue();
});

test('user cannot update password with wrong current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword123'),
    ]);
    $this->actingAs($user);

    $response = $this->put(route('password.update'), [
        'current_password' => 'wrongpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors('current_password');

    $updatedUser = User::find($user->id);
    expect(Hash::check('oldpassword123', $updatedUser->password))->toBeTrue();
});

test('user cannot update password with mismatched confirmation', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword123'),
    ]);
    $this->actingAs($user);

    $response = $this->put(route('password.update'), [
        'current_password' => 'oldpassword123',
        'password' => 'newpassword123',
        'password_confirmation' => 'differentpassword',
    ]);

    $response->assertSessionHasErrors('password');

    $updatedUser = User::find($user->id);
    expect(Hash::check('oldpassword123', $updatedUser->password))->toBeTrue();
});

test('user cannot update password with weak password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword123'),
    ]);
    $this->actingAs($user);

    $response = $this->put(route('password.update'), [
        'current_password' => 'oldpassword123',
        'password' => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');

    $updatedUser = User::find($user->id);
    expect(Hash::check('oldpassword123', $updatedUser->password))->toBeTrue();
});
