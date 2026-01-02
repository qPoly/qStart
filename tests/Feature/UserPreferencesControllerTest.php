<?php

use App\Models\User;

test('guests cannot update page preferences', function () {
    $response = $this->put(route('pagePreferences.update', 'test-page'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can update page preferences', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->put(route('pagePreferences.update', 'users'), [
        'sortColumn' => 'name',
        'sortDirection' => 'desc',
        'perPage' => 25,
    ]);

    $response->assertStatus(204);
});

test('page preferences are stored in user preferences', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->put(route('pagePreferences.update', 'users'), [
        'sortColumn' => 'name',
        'sortDirection' => 'desc',
        'perPage' => 25,
    ]);

    $user->refresh();
    expect($user->preferences)->toBeArray();
    expect($user->preferences['pages']['users'])->toBeArray();
    expect($user->preferences['pages']['users'])->toHaveKeys(['sortColumn', 'sortDirection', 'perPage', 'columns']);
    expect($user->preferences['pages']['users']['sortColumn'])->toBe('name');
    expect($user->preferences['pages']['users']['sortDirection'])->toBe('desc');
    expect($user->preferences['pages']['users']['perPage'])->toBe(25);
});
