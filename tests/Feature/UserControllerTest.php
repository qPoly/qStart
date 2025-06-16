<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can view users index page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('users.index'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($assert) => $assert
            ->component('users/Index')
            ->has('users')
    );
});

test('user can view create user page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('users.create'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($assert) => $assert
            ->component('users/CreateEdit')
    );
});

test('user can create new user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('users.store'), [
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ]);

    $createdUser = User::where('email', 'new@example.com')->first();
    expect(Hash::check('password123', $createdUser->password))->toBeTrue();
});

test('user can view edit user page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('users.edit', $user));

    $response->assertStatus(200);
    $response->assertInertia(
        fn($assert) => $assert
            ->component('users/CreateEdit')
            ->has('user')
    );
});

test('user can update existing user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $updatedUser = User::find($user->id);
    expect(Hash::check('newpassword123', $updatedUser->password))->toBeTrue();
});

test('user can update without changing password', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->put(route('users.update', $user), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('user can delete user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->delete(route('users.destroy', $user));

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
