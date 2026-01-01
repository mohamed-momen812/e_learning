<?php

namespace Modules\Admin\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;

class RoleService
{
    /**
     * Create a role with explicit guard
     */
    public function createRole(string $name, array $permissions = []): Model
    {
        // Get the next display_order value
        $maxOrder = Role::where('guard_name', 'web')->max('display_order') ?? 0;
        
        $role = Role::create([
            'name' => $name,
            'guard_name' => 'web', // Always use 'web' guard
            'display_order' => $maxOrder + 1,
        ]);

        if (!empty($permissions)) {
            $role->givePermissionTo($permissions);
        }

        return $role->fresh(['permissions']);
    }

    /**
     * Update role
     */
    public function updateRole(string $id, ?string $name = null, array $permissions = []): Model
    {
        $role = Role::findOrFail($id);

        if ($name !== null) {
            $role->update(['name' => $name]);
        }

        // Sync permissions if provided
        if (!empty($permissions) || (isset($permissions) && empty($permissions))) {
            $role->syncPermissions($permissions);
        }

        return $role->fresh(['permissions']);
    }

    /**
     * Delete role
     */
    public function deleteRole(string $id): bool
    {
        $role = Role::findOrFail($id);
        return $role->delete();
    }

    /**
     * Find role by ID
     */
    public function find(string $id): ?Model
    {
        return Role::with('permissions')->find($id);
    }

    /**
     * Find or fail
     */
    public function findOrFail(string $id): Model
    {
        return Role::with('permissions')->findOrFail($id);
    }
}

