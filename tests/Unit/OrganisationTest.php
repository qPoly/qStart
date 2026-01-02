<?php

use App\Models\Organisation;
use App\Models\User;

test('organisation has many users', function () {
    $organisation = Organisation::factory()->create();
    User::factory()->count(3)->create(['organisation_id' => $organisation->id]);

    expect($organisation->users)->toHaveCount(3);
    expect($organisation->users->first())->toBeInstanceOf(User::class);
});

test('organisation can be soft deleted', function () {
    $organisation = Organisation::factory()->create();
    $organisation->delete();

    expect($organisation->trashed())->toBeTrue();
    $this->assertSoftDeleted('organisations', ['id' => $organisation->id]);
});

test('organisation has page columns configuration', function () {
    $organisation = Organisation::factory()->create();
    $columns = $organisation->getPageColumns();

    expect($columns)->toBeArray();
    expect($columns)->toHaveCount(5);
    expect($columns[0]['key'])->toBe('id');
    expect($columns[2]['key'])->toBe('name');
});

test('organisation fillable attributes are name and logo_path', function () {
    $organisation = Organisation::factory()->create([
        'name' => 'Test Organisation',
        'logo_path' => 'images/logos/test.jpg',
    ]);

    expect($organisation->name)->toBe('Test Organisation');
    expect($organisation->logo_path)->toBe('images/logos/test.jpg');
});
