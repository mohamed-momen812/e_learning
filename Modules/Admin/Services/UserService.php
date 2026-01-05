<?php

namespace Modules\Admin\Services;

use App\Models\Role;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    public function __construct(
        protected ImageService $imageService
    ) {}

    /**
     * Create a new user (prevents assigning admin roles)
     */
    public function create(array $data, ?UploadedFile $avatar = null): Model
    {
        // Get the next display_order value
        $maxOrder = User::max('display_order') ?? 0;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'display_order' => $maxOrder + 1,
        ]);

        // Handle avatar upload if provided
        if ($avatar && $avatar->isValid()) {
            $this->imageService->uploadAndAttach($user, $avatar, 'avatar');
        }

        // Assign roles if provided (but filter out admin roles)
        // Admin roles are those that have permissions only teachers should have
        if (isset($data['roles']) && is_array($data['roles'])) {
            // Filter out roles that have admin-only permissions (like roles.create)
            $filteredRoles = array_filter($data['roles'], function ($roleName) {
                $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
                if (!$role) {
                    return false;
                }
                // Filter out roles that have admin-only permissions
                return !$role->hasPermissionTo('roles.create');
            });
            if (!empty($filteredRoles)) {
                $user->assignRole($filteredRoles);
            }
        }

        return $user->fresh(['roles', 'avatar']);
    }

    /**
     * Update user (excludes admin users)
     */
    public function update(string $id, array $data, ?UploadedFile $avatar = null): Model
    {
        $user = User::with('roles')->findOrFail($id);

        // Prevent updating admin users (users with admin-only permissions)
        if ($user->can('roles.create')) {
            abort(404, __('user.cannot_update_admin_user'));
        }

        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }

        if (isset($data['phone'])) {
            $updateData['phone'] = $data['phone'];
        }

        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }

        // Handle avatar upload if provided
        if ($avatar && $avatar->isValid()) {
            $this->imageService->uploadAndAttach($user, $avatar, 'avatar');
        }

        // Sync roles if provided (but prevent assigning admin roles)
        if (isset($data['roles']) && is_array($data['roles'])) {
            // Filter out roles that have admin-only permissions
            $filteredRoles = array_filter($data['roles'], function ($roleName) {
                $role = \App\Models\Role::where('name', $roleName)->where('guard_name', 'web')->first();
                if (!$role) {
                    return false;
                }
                // Filter out roles that have admin-only permissions
                return !$role->hasPermissionTo('roles.create');
            });
            $user->syncRoles($filteredRoles);
        }

        return $user->fresh(['roles', 'avatar']);
    }

    /**
     * Delete user (excludes admin users)
     */
    public function delete(string $id): bool
    {
        $user = User::with('roles')->findOrFail($id);

        // Prevent deleting admin users (users with admin-only permissions)
        if ($user->can('roles.create')) {
            abort(404, __('user.cannot_delete_admin_user'));
        }

        return $user->delete();
    }

    /**
     * Bulk delete users (excludes admin users)
     */
    public function bulkDelete(array $ids): array
    {
        $users = User::with('roles')->whereIn('id', $ids)->get();

        $deleted = [];
        $skipped = [];

        foreach ($users as $user) {
            // Prevent deleting admin users (users with admin-only permissions)
            if ($user->can('roles.create')) {
                $skipped[] = [
                    'id' => $user->id,
                    'reason' => __('user.cannot_delete_admin_user')
                ];
                continue;
            }

            $user->delete();
            $deleted[] = $user->id;
        }

        return [
            'deleted' => $deleted,
            'skipped' => $skipped,
            'deleted_count' => count($deleted),
            'skipped_count' => count($skipped),
        ];
    }

    /**
     * Find user by ID (excludes admin users)
     */
    public function find(string $id): ?Model
    {
        $user = User::with('roles')->find($id);

        // Exclude admin users (users with admin-only permissions)
        if ($user && $user->can('roles.create')) {
            return null;
        }

        return $user;
    }

    /**
     * Find or fail (excludes admin users)
     */
    public function findOrFail(string $id): Model
    {
        $user = User::with('roles')->findOrFail($id);

        // Exclude admin users (users with admin-only permissions)
        if ($user->can('roles.create')) {
            abort(404, __('user.cannot_find_admin_user'));
        }

        return $user;
    }
}
