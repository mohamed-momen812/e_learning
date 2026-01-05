<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine if the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determine if the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determine if the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    /**
     * Determine if the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('roles.update');
    }

    /**
     * Determine if the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can('roles.delete');
    }

    /**
     * Determine if the user can bulk delete roles.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->can('roles.bulk_delete');
    }

    /**
     * Determine if the user can update display order for roles.
     */
    public function updateOrder(User $user): bool
    {
        return $user->can('roles.update_order');
    }
}
