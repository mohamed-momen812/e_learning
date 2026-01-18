<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class RoleService
{
    /**
     * Create a role with explicit guard
     */
    public function createRole(string $name, array $permissions = [], ?array $label = null): Model
    {
        // Get the next display_order value
        $maxOrder = Role::where('guard_name', 'web')->max('display_order') ?? 0;

        $data = [
            'name' => $name,
            'guard_name' => 'web', // Always use 'web' guard
            'display_order' => $maxOrder + 1,
        ];

        if ($label !== null) {
            $data['label'] = $label;
        }

        $role = Role::create($data);

        if (! empty($permissions)) {
            $role->givePermissionTo($permissions);
        }

        return $role->fresh(['permissions']);
    }

    /**
     * Update role
     */
    public function updateRole(string $id, ?string $name = null, array $permissions = [], ?array $label = null): Model
    {
        $role = Role::findOrFail($id);

        $data = [];
        if ($name !== null) {
            $data['name'] = $name;
        }
        if ($label !== null) {
            $data['label'] = $label;
        }

        if (! empty($data)) {
            $role->update($data);
        }

        // Sync permissions if provided
        if (! empty($permissions) || (isset($permissions) && empty($permissions))) {
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
     * Bulk delete roles
     */
    public function bulkDeleteRoles(array $ids): array
    {
        $roles = Role::whereIn('id', $ids)->get();

        $deleted = [];
        $skipped = [];

        foreach ($roles as $role) {
            try {
                $role->delete();
                $deleted[] = $role->id;
            } catch (\Exception $e) {
                $skipped[] = [
                    'id' => $role->id,
                    'reason' => $e->getMessage(),
                ];
            }
        }

        return [
            'deleted' => $deleted,
            'skipped' => $skipped,
            'deleted_count' => count($deleted),
            'skipped_count' => count($skipped),
        ];
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
