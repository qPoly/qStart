<?php

use App\Models\Organisation;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles and permissions
    Permission::firstOrCreate(['name' => 'manage users']);
    Permission::firstOrCreate(['name' => 'manage organisations']);
    Role::firstOrCreate(['name' => 'admin'])->syncPermissions(['manage users', 'manage organisations']);
    Role::firstOrCreate(['name' => 'user'])->givePermissionTo('manage users');
});

test('guests cannot access users index', function () {
    $response = $this->get(route('users.index'));
    $response->assertRedirect(route('login'));
});

test('users without permission cannot access users index', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('users.index'));
    $response->assertForbidden();
});

test('users with permission can view users index', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    User::factory()->count(3)->create();

    $response = $this->get(route('users.index'));
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('users/Index')
            ->has('users.data')
    );
});

test('users index filters by organisation when user has organisation_id', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    User::factory()->create(['organisation_id' => $organisation->id]);
    User::factory()->create(['organisation_id' => null]);
    User::factory()->create(['organisation_id' => Organisation::factory()->create()->id]);

    $response = $this->get(route('users.index'));
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('users/Index')
            ->has('users.data', 2) // Only the user with same organisation_id
    );
});

test('users index shows only users without organisation when user has no organisation_id', function () {
    $user = User::factory()->create(['organisation_id' => null]);
    $user->assignRole('admin');
    $this->actingAs($user);

    User::factory()->create(['organisation_id' => null]);
    User::factory()->create(['organisation_id' => Organisation::factory()->create()->id]);

    $response = $this->get(route('users.index'));
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('users/Index')
            ->has('users.data', 2) // Only users without organisation
    );
});

test('users index can be filtered by search', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

    $response = $this->get(route('users.index', ['search' => 'John']));
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.name', 'John Doe')
    );
});

test('users index search filters by email', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

    $response = $this->get(route('users.index', ['search' => 'jane@example.com']));
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.email', 'jane@example.com')
    );
});

test('users with permission can view create user form', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $response = $this->get(route('users.create'));
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('users/CreateEdit')
    );
});

test('users with permission can create a user in admin workspace', function () {
    $user = User::factory()->create(['organisation_id' => null]);
    $user->assignRole('admin');
    $this->actingAs($user);

    $response = $this->post(route('users.store'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'organisation_id' => null,
    ]);

    $newUser = User::where('email', 'newuser@example.com')->first();
    expect($newUser->hasRole('admin'))->toBeTrue();
});

test('users with permission can create a user in organisation workspace', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $response = $this->post(route('users.store'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'organisation_id' => $organisation->id,
    ]);

    $newUser = User::where('email', 'newuser@example.com')->first();
    expect($newUser->hasRole('user'))->toBeTrue();
});

test('users with permission can view edit user form', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $user->organisation_id]);

    $response = $this->get(route('users.edit', $targetUser));
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('users/CreateEdit')
            ->where('user.id', $targetUser->id)
    );
});

test('users cannot edit users from different organisations', function () {
    $organisation1 = Organisation::factory()->create();
    $organisation2 = Organisation::factory()->create();

    $user = User::factory()->create(['organisation_id' => $organisation1->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $organisation2->id]);

    $response = $this->get(route('users.edit', $targetUser));
    $response->assertForbidden();
});

test('users with permission can update a user', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $organisation->id, 'name' => 'Old Name']);

    $response = $this->put(route('users.update', $targetUser), [
        'name' => 'New Name',
        'email' => $targetUser->email,
    ]);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'name' => 'New Name',
    ]);
});

test('users can update a user without changing password', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $organisation->id]);
    $oldPassword = $targetUser->password;

    $response = $this->put(route('users.update', $targetUser), [
        'name' => 'New Name',
        'email' => $targetUser->email,
        'password' => '',
    ]);

    $response->assertRedirect(route('users.index'));
    $targetUser->refresh();
    expect($targetUser->password)->toBe($oldPassword);
});

test('users can update a user with new password', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $organisation->id]);
    $oldPassword = $targetUser->password;

    $response = $this->put(route('users.update', $targetUser), [
        'name' => $targetUser->name,
        'email' => $targetUser->email,
        'password' => 'NewPassword123!',
    ]);

    $response->assertRedirect(route('users.index'));
    $targetUser->refresh();
    expect($targetUser->password)->not->toBe($oldPassword);
    expect(\Illuminate\Support\Facades\Hash::check('NewPassword123!', $targetUser->password))->toBeTrue();
});

test('users cannot update users from different organisations', function () {
    $organisation1 = Organisation::factory()->create();
    $organisation2 = Organisation::factory()->create();

    $user = User::factory()->create(['organisation_id' => $organisation1->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $organisation2->id]);

    $response = $this->put(route('users.update', $targetUser), [
        'name' => 'New Name',
        'email' => $targetUser->email,
    ]);

    $response->assertForbidden();
});

test('users with permission can delete a user', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $organisation->id]);
    $targetUser->assignRole('user');

    $response = $this->delete(route('users.destroy', $targetUser));

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseMissing('users', [
        'id' => $targetUser->id,
    ]);
});

test('users cannot delete users from different organisations', function () {
    $organisation1 = Organisation::factory()->create();
    $organisation2 = Organisation::factory()->create();

    $user = User::factory()->create(['organisation_id' => $organisation1->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $organisation2->id]);

    $response = $this->delete(route('users.destroy', $targetUser));

    $response->assertForbidden();
});

test('user creation requires name', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $response = $this->post(route('users.store'), [
        'email' => 'test@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('name');
});

test('user creation requires email', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $response = $this->post(route('users.store'), [
        'name' => 'Test User',
        'password' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('email');
});

test('user creation requires password', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $response = $this->post(route('users.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors('password');
});

test('user email must be unique', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post(route('users.store'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('email');
});

test('user email can be updated to same value', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create([
        'organisation_id' => $organisation->id,
        'email' => 'test@example.com',
    ]);

    $response = $this->put(route('users.update', $targetUser), [
        'name' => 'Updated Name',
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect(route('users.index'));
    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'email' => 'test@example.com',
    ]);
});
