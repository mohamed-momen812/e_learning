<?php

namespace App\Services;

use App\Core\Exceptions\BusinessException;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

/**
 * Service for managing direct user permissions.
 *
 * Direct permissions are assigned directly to users (not via roles) and are
 * additive to role-based permissions. They should be used for exceptions only.
 */
class DirectPermissionService
{
    /**
     * Assign direct permission(s) to a user with logging and validation.
     *
     * @param User $user The user to assign permissions to
     * @param array $permissions Permission names
     * @return void
     * @throws BusinessException If permissions are invalid
     */
    public function assign(User $user, array $permissions): void
    {
        $this->validatePermissions($permissions);

        DB::transaction(function () use ($user, $permissions) {
            foreach ($permissions as $permissionName) {
                // Check if user already has this permission via role
                if ($user->hasPermissionTo($permissionName)) {
                    throw new BusinessException(
                        "permissions.user_already_has_permission",
                        ['permission' => $permissionName],
                        409
                    );
                }

                $user->givePermissionTo($permissionName);

                // Log the assignment
                $assignedBy = Auth::check() ? Auth::id() : 'system';
                Log::info('Direct permission assigned to user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'permission' => $permissionName,
                    'assigned_by' => $assignedBy,
                ]);
            }
        });
    }

    /**
     * Revoke direct permission(s) from a user with logging.
     *
     * @param User $user The user to revoke permissions from
     * @param array $permissions Permission names
     * @return void
     * @throws BusinessException If permissions are invalid
     */
    public function revoke(User $user, array $permissions): void
    {
        $this->validatePermissions($permissions);

        DB::transaction(function () use ($user, $permissions) {
            foreach ($permissions as $permissionName) {
                // Check if user has this permission
                if (!$user->hasPermissionTo($permissionName)) {
                    throw new BusinessException(
                        "permissions.user_does_not_have_permission",
                        ['permission' => $permissionName],
                        409
                    );
                }

                $user->revokePermissionTo($permissionName);

                // Log the revocation
                $revokedBy = Auth::check() ? Auth::id() : 'system';
                Log::info('Direct permission revoked from user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'permission' => $permissionName,
                    'revoked_by' => $revokedBy,
                ]);
            }
        });
    }

    /**
     * Get all direct permissions for a user (excluding role-based permissions).
     *
     * @param User $user
     * @return Collection
     */
    public function getDirectPermissions(User $user): Collection
    {
        // Get all permissions the user has
        $allPermissions = $user->getAllPermissions();

        // Get permissions from roles
        $rolePermissions = $user->getPermissionsViaRoles();

        // Return only direct permissions (not from roles)
        return $allPermissions->diff($rolePermissions);
    }

    /**
     * Validate that permissions exist and follow naming conventions.
     *
     * @param array $permissionNames
     * @return void
     * @throws BusinessException If permissions are invalid
     */
    protected function validatePermissions(array $permissionNames): void
    {
        foreach ($permissionNames as $permissionName) {
            // Validate naming convention (entity.action format)
            if (!preg_match('/^[a-z_]+\.[a-z_]+$/', $permissionName)) {
                throw new BusinessException(
                    "permissions.invalid_permission_name_format",
                    [],
                    400
                );
            }

            // Check if permission exists
            $permission = Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->first();

            if (!$permission) {
                throw new BusinessException(
                    "permissions.permission_not_found",
                    ['permission' => $permissionName],
                    404
                );
            }
        }
    }
}
