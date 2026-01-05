<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Check permission for viewing other users
        return $user->can('users.view');
    }

    /**
     * Determine if the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    /**
     * Determine if the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Check permission for updating other users
        return $user->can('users.update');
    }

    /**
     * Determine if the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Users cannot delete themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Check permission for deleting users
        return $user->can('users.delete');
    }

    /**
     * Determine if the user can bulk delete users.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->can('users.bulk_delete');
    }

    /**
     * Determine if the user can update display order (bulk operation).
     */
    public function updateOrder(User $user): bool
    {
        return $user->can('users.update_order');
    }

    /**
     * Determine if the user can manage direct permissions for users.
     */
    public function managePermissions(User $user): bool
    {
        return $user->can('users.manage_permissions');
    }
}

