<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\UserPreferencesService;

class UserPreferencesController extends Controller
{
    /**
     * Update the user's page preferences.
     */
    public function update(string $page, UserPreferencesService $userPreferencesService)
    {
        $userPreferencesService->getAndUpdatePagePreferences($page);

        return response()->noContent();
    }
}
