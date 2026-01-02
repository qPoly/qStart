<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can edit, update or delete the model.
     */
    public function mutate(User $user, User $model): bool
    {
        if ($user->can('manage users') && $model->organisation_id === $user->organisation_id) {
            return true;
        }

        return false;
    }
}
