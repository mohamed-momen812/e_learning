<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tenant;

class TenantPolicy
{
    /**
     * Determine if the user can view any tenants.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('tenants.view');
    }

    /**
     * Determine if the user can view the tenant.
     */
    public function view(User $user, Tenant $tenant): bool
    {
        return $user->can('tenants.view');
    }

    /**
     * Determine if the user can create tenants.
     */
    public function create(User $user): bool
    {
        return $user->can('tenants.create');
    }

    /**
     * Determine if the user can update the tenant.
     */
    public function update(User $user, Tenant $tenant): bool
    {
        return $user->can('tenants.update');
    }

    /**
     * Determine if the user can delete the tenant.
     */
    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->can('tenants.delete');
    }

    /**
     * Determine if the user can bulk delete tenants.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->can('tenants.bulk_delete');
    }
}
