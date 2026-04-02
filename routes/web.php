<?php

use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\Settings\UserPreferencesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/', '/dashboard')->name('home');

    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::put('page-preferences/{page}', [UserPreferencesController::class, 'update'])->name('pagePreferences.update');

    Route::get('organisations/{organisationId}/switch', [OrganisationController::class, 'switch'])->name('organisations.switch');

    Route::resource('organisaties', OrganisationController::class)->names('organisations')->parameters([
        'organisaties' => 'organisation'
    ]);

    Route::resource('gebruikers', UserController::class)->names('users')->parameters([
        'gebruikers' => 'user'
    ]);
});

require __DIR__ . '/settings.php';
