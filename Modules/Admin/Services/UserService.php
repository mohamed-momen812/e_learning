<?php

namespace Modules\Admin\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserService
{
    /**
     * Create a new user
     */
    public function create(array $data): Model
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        // Assign roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->assignRole($data['roles']);
        }

        return $user->fresh(['roles']);
    }

    /**
     * Update user
     */
    public function update(string $id, array $data): Model
    {
        $user = User::findOrFail($id);

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

        // Sync roles if provided
        if (isset($data['roles']) && is_array($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return $user->fresh(['roles']);
    }

    /**
     * Delete user
     */
    public function delete(string $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    /**
     * Find user by ID
     */
    public function find(string $id): ?Model
    {
        return User::with('roles')->find($id);
    }

    /**
     * Find or fail
     */
    public function findOrFail(string $id): Model
    {
        return User::with('roles')->findOrFail($id);
    }
}

