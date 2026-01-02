<?php

use App\Http\Requests\OrganisationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Permission::firstOrCreate(['name' => 'manage organisations']);
    Role::firstOrCreate(['name' => 'admin'])->givePermissionTo('manage organisations');
});

test('organisation request validates name is required', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new OrganisationRequest();
    $rules = $request->rules();

    $validator = Validator::make([], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('organisation request validates name is string', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new OrganisationRequest();
    $rules = $request->rules();

    $validator = Validator::make(['name' => 123], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('organisation request validates name max length', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new OrganisationRequest();
    $rules = $request->rules();

    $validator = Validator::make(['name' => str_repeat('a', 256)], $rules);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

test('organisation request accepts valid name', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new OrganisationRequest();
    $rules = $request->rules();

    $validator = Validator::make(['name' => 'Valid Organisation Name'], $rules);

    expect($validator->fails())->toBeFalse();
});

test('organisation request allows nullable logo_path', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $request = new OrganisationRequest();
    $rules = $request->rules();

    $validator = Validator::make([
        'name' => 'Test Organisation',
        'logo_path' => null,
    ], $rules);

    expect($validator->fails())->toBeFalse();
});

test('organisation request has custom error messages', function () {
    $request = new OrganisationRequest();
    $messages = $request->messages();

    expect($messages)->toHaveKey('name.required');
    expect($messages)->toHaveKey('name.string');
    expect($messages)->toHaveKey('name.max');
    expect($messages['name.required'])->toBe('Naam is verplicht');
});

