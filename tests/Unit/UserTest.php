<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user has required fillable attributes', function () {
    $user = new User();

    expect($user->getFillable())->toContain('name')
        ->and($user->getFillable())->toContain('email')
        ->and($user->getFillable())->toContain('password');
});

test('user has required hidden attributes', function () {
    $user = new User();

    expect($user->getHidden())->toContain('password')
        ->and($user->getHidden())->toContain('remember_token');
});
