<?php

namespace Modules\Admin\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    /**
     * Create a new user (prevents assigning admin roles)
     */
    public function create(array $data): Model
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

        // Assign roles if provided (but filter out admin roles)
        if (isset($data['roles']) && is_array($data['roles'])) {
            // Filter out admin roles
            $filteredRoles = array_filter($data['roles'], function ($role) {
                return !in_array($role, ['teacher']);
            });
            if (!empty($filteredRoles)) {
                $user->assignRole($filteredRoles);
            }
        }

        return $user->fresh(['roles']);
    }

    /**
     * Update user (excludes admin users)
     */
    public function update(string $id, array $data): Model
    {
        $user = User::with('roles')->findOrFail($id);

        // Prevent updating admin users
        if ($user->hasAnyRole(['teacher'])) {
            abort(404, 'User is an admin, cannot be updated');
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

        // Sync roles if provided (but prevent assigning admin roles)
        if (isset($data['roles']) && is_array($data['roles'])) {
            // Filter out admin roles
            $filteredRoles = array_filter($data['roles'], function ($role) {
                return !in_array($role, ['teacher']);
            });
            $user->syncRoles($filteredRoles);
        }

        return $user->fresh(['roles']);
    }

    /**
     * Delete user (excludes admin users)
     */
    public function delete(string $id): bool
    {
        $user = User::with('roles')->findOrFail($id);

        // Prevent deleting admin users
        if ($user->hasAnyRole(['teacher'])) {
            abort(404, 'Admin User cannot be deleted');
        }

        return $user->delete();
    }

    /**
     * Find user by ID (excludes admin users)
     */
    public function find(string $id): ?Model
    {
        $user = User::with('roles')->find($id);

        if ($user && $user->hasAnyRole(['teacher'])) {
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

        if ($user->hasAnyRole(['teacher'])) {
            abort(404, 'Can not find admin user');
        }

        return $user;
    }
}

