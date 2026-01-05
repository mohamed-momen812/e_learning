<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permission;

class PermissionPolicy
{
    /**
     * Determine if the user can view any permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('permissions.view');
    }

    /**
     * Determine if the user can view the permission.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $user->can('permissions.view');
    }

    /**
     * Determine if the user can update display order for permissions.
     */
    public function updateOrder(User $user): bool
    {
        return $user->can('permissions.update_order');
    }
}

