<?php

use App\Models\Organisation;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles and permissions
    Permission::firstOrCreate(['name' => 'manage users']);
    Role::firstOrCreate(['name' => 'admin'])->givePermissionTo('manage users');
    Role::firstOrCreate(['name' => 'user'])->givePermissionTo('manage users');
});

test('user can mutate user in same organisation', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    
    $targetUser = User::factory()->create(['organisation_id' => $organisation->id]);

    expect($user->can('mutate', $targetUser))->toBeTrue();
});

test('user cannot mutate user in different organisation', function () {
    $organisation1 = Organisation::factory()->create();
    $organisation2 = Organisation::factory()->create();
    
    $user = User::factory()->create(['organisation_id' => $organisation1->id]);
    $user->assignRole('user');
    
    $targetUser = User::factory()->create(['organisation_id' => $organisation2->id]);

    expect($user->can('mutate', $targetUser))->toBeFalse();
});

test('user without manage users permission cannot mutate user', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    // User has no role/permissions
    
    $targetUser = User::factory()->create(['organisation_id' => $organisation->id]);

    expect($user->can('mutate', $targetUser))->toBeFalse();
});

test('user can mutate user when both have null organisation_id', function () {
    $user = User::factory()->create(['organisation_id' => null]);
    $user->assignRole('admin');
    
    $targetUser = User::factory()->create(['organisation_id' => null]);

    expect($user->can('mutate', $targetUser))->toBeTrue();
});

test('user cannot mutate user when user has organisation_id but target has null', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => $organisation->id]);
    $user->assignRole('user');
    
    $targetUser = User::factory()->create(['organisation_id' => null]);

    expect($user->can('mutate', $targetUser))->toBeFalse();
});

test('user cannot mutate user when user has null but target has organisation_id', function () {
    $organisation = Organisation::factory()->create();
    $user = User::factory()->create(['organisation_id' => null]);
    $user->assignRole('admin');
    
    $targetUser = User::factory()->create(['organisation_id' => $organisation->id]);

    expect($user->can('mutate', $targetUser))->toBeFalse();
});

