<?php

use App\Http\Requests\UserRequest;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage users']);
    Role::firstOrCreate(['name' => 'admin'])->givePermissionTo('manage users');
});

test('user request validates name is required', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new UserRequest();
    $rules = $request->rules();

    $validator = Validator::make([], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('user request validates email is required', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new UserRequest();
    $rules = $request->rules();

    $validator = Validator::make(['name' => 'Test User'], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('user request validates email format', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new UserRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'Password123!',
    ], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('user request validates email uniqueness on create', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $request = new UserRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'Password123!',
    ], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('email'))->toBeTrue();
});

test('user request allows same email on update', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['email' => 'test@example.com']);

    // Test via actual HTTP request to properly test route binding
    $response = $this->put(route('users.update', $targetUser), [
        'name' => 'Updated Name',
        'email' => 'test@example.com',
    ]);

    // Should not have email validation error
    $response->assertSessionDoesntHaveErrors('email');
});

test('user request requires password on create', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new UserRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('password'))->toBeTrue();
});

test('user request allows nullable password on update', function () {
    Permission::firstOrCreate(['name' => 'manage users']);
    Role::firstOrCreate(['name' => 'user'])->givePermissionTo('manage users');

    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    $this->actingAs($user);

    $targetUser = User::factory()->create(['organisation_id' => $organisation->id]);

    // Test via actual HTTP request - password should be optional
    $response = $this->put(route('users.update', $targetUser), [
        'name' => 'Updated Name',
        'email' => $targetUser->email,
        'password' => '',
    ]);

    // Should succeed without password
    $response->assertRedirect(route('users.index'));
    $response->assertSessionDoesntHaveErrors('password');
});

test('user request validates password meets requirements', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new UserRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
    ], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('password'))->toBeTrue();
});

test('user request accepts valid data', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new UserRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
    ], $rules);

    expect($validator->fails())->toBeFalse();
});

test('user request has custom error messages', function () {
    $request = new UserRequest();
    $messages = $request->messages();

    expect($messages)->toHaveKey('name.required');
    expect($messages)->toHaveKey('email.required');
    expect($messages)->toHaveKey('email.email');
    expect($messages)->toHaveKey('email.unique');
    expect($messages)->toHaveKey('password.required');
    expect($messages['name.required'])->toBe('Naam is verplicht');
    expect($messages['email.required'])->toBe('E-mailadres is verplicht');
});
