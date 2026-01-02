<?php

use App\Models\Organisation;
use App\Models\User;

test('user belongs to organisation', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);

    expect($user->organisation)->toBeInstanceOf(Organisation::class);
    expect($user->organisation->id)->toBe($organisation->id);
});

test('user can have no organisation', function () {
    $user = User::factory()->create(['organisation_id' => null]);

    expect($user->organisation)->toBeNull();
});

test('user has page columns configuration', function () {
    $user = User::factory()->create();
    $columns = $user->getPageColumns();

    expect($columns)->toBeArray();
    expect($columns)->toHaveCount(6);
    expect($columns[0]['key'])->toBe('id');
    expect($columns[2]['key'])->toBe('name');
    expect($columns[3]['key'])->toBe('email');
});

test('user password is hashed', function () {
    $user = User::factory()->create(['password' => 'plain-password']);

    expect($user->password)->not->toBe('plain-password');
    expect(password_verify('plain-password', $user->password))->toBeTrue();
});

test('user has roles trait', function () {
    $user = User::factory()->create();
    
    expect(method_exists($user, 'hasRole'))->toBeTrue();
    expect(method_exists($user, 'assignRole'))->toBeTrue();
});

test('user preferences can be stored as array', function () {
    $user = User::factory()->create([
        'preferences' => [
            'test-page' => [
                'sortColumn' => 'name',
                'sortDirection' => 'asc',
            ],
        ],
    ]);

    expect($user->preferences)->toBeArray();
    expect($user->preferences['test-page']['sortColumn'])->toBe('name');
});

test('user fillable attributes are name, email, and password', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->password)->not->toBeNull();
});

