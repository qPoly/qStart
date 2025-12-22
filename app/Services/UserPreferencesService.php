<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPreferencesService
{
    /**
     * Get and update the user's page preferences for a specific page.
     */
    public static function getAndUpdatePagePreferences(string $page): array
    {
        $model = self::getModelForPage($page);
        $columns = $model->getPageColumns();
        $user = request()->user();

        // Get user's page preferences
        $userPreferences = $user->preferences['pages'][$page] ?? [];

        // Sync model columns
        $userPreferences['columns'] = self::syncModelColumns($columns, request()->input('columns', $userPreferences['columns'] ?? $columns));

        // Get default sort column and direction
        [$defaultSortColumn, $defaultSortDirection] = self::getDefaultSort($columns);

        // Get and validate sort direction
        $userPreferences['sortDirection'] = request()->input('sortDirection', $userPreferences['sortDirection'] ?? $defaultSortDirection);
        $userPreferences['sortDirection'] = in_array(strtolower($userPreferences['sortDirection']), ['asc', 'desc']) ? $userPreferences['sortDirection'] : 'asc';

        // Get and validate sort column
        $sortableColumns = array_filter($columns, function ($column) {
            return empty($column['unsortable']);
        });
        $columnKeys = array_column($sortableColumns, 'key');
        $userPreferences['sortColumn'] = request()->input('sortColumn', $userPreferences['sortColumn'] ?? $defaultSortColumn);
        $userPreferences['sortColumn'] = in_array($userPreferences['sortColumn'], $columnKeys) ? $userPreferences['sortColumn'] : $defaultSortColumn;

        // Get and validate number of items per page
        $defaultPerPage = 15;
        $userPreferences['perPage'] = request()->input('perPage', $userPreferences['perPage'] ?? $defaultPerPage);
        $userPreferences['perPage'] = is_numeric($userPreferences['perPage']) ? (int) $userPreferences['perPage'] : $defaultPerPage;

        // Update user preferences
        $preferences = $user->preferences ?? [];
        $preferences['pages'][$page] = $userPreferences;
        $user->preferences = $preferences;
        $user->save();

        // Return preferences and columns (with visibility)
        return [
            'columns' => $userPreferences['columns'],
            'sortDirection' => $userPreferences['sortDirection'],
            'sortColumn' => $userPreferences['sortColumn'],
            'perPage' => $userPreferences['perPage'],
        ];
    }

    /**
     * Get the model instance for a given page.
     */
    private static function getModelForPage(string $page): ?Model
    {
        switch ($page) {
            case 'users':
                return new User;
            default:
                return null;
        }
    }

    /**
     * Extract the default sort column and direction from columns definition.
     */
    private static function getDefaultSort(array $columns): array
    {
        $defaultSortColumn = 'id';
        $defaultSortDirection = 'asc';

        foreach ($columns as $column) {
            if (isset($column['default']) && $column['default'] == true) {
                $defaultSortColumn = $column['key'];
                $defaultSortDirection = $column['default'];
                break;
            }
        }

        return [$defaultSortColumn, $defaultSortDirection];
    }

    /**
     * Sync the columns from the model with the user's preferences, ensuring model columns are leading.
     */
    private static function syncModelColumns(array $modelColumns, $userColumns): array
    {
        // Extract keys from model columns
        $modelColumnKeys = array_column($modelColumns, 'key');

        // Build a map of model columns by key for quick lookup
        $modelColumnsByKey = [];

        foreach ($modelColumns as $col) {
            $modelColumnsByKey[$col['key']] = $col;
        }

        // Build a map of user columns by key for quick lookup
        $userColumnsByKey = [];
        $userColumnOrder = [];

        if (is_array($userColumns)) {
            foreach ($userColumns as $col) {
                if (is_array($col) && isset($col['key']) && in_array($col['key'], $modelColumnKeys)) {
                    $userColumnsByKey[$col['key']] = $col;
                    $userColumnOrder[] = $col['key'];
                }
            }
        }

        $syncedColumns = [];

        // First, add columns in the order from user preferences, if they exist in the model
        foreach ($userColumnOrder as $key) {
            if (isset($modelColumnsByKey[$key])) {
                $syncedColumns[] = array_merge($userColumnsByKey[$key], $modelColumnsByKey[$key]);
            }
        }

        // Then, append any new model columns not present in user preferences
        foreach ($modelColumnKeys as $key) {
            if (!in_array($key, $userColumnOrder)) {
                $syncedColumns[] = $modelColumnsByKey[$key];
            }
        }

        return $syncedColumns;
    }
}
