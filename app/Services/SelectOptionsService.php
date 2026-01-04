<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SelectOptionsService
{
    /**
     * Cache duration in seconds (5 minutes)
     */
    protected const CACHE_DURATION = 300;

    /**
     * Get cache key with tenant prefix if in tenant context
     */
    protected function getCacheKey(string $key): string
    {
        $prefix = '';

        // If tenancy is initialized, add tenant ID to cache key
        if (tenancy()->initialized) {
            $tenantId = tenant('id');
            $prefix = "tenant_{$tenantId}_";
        }

        return $prefix . $key;
    }

    /**
     * Get all select options
     */
    public function getAll(): array
    {
        $cacheKey = $this->getCacheKey('select_options_all');

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return [
                'roles' => $this->getRoles(),
                'permissions' => $this->getPermissions(),
                'users' => $this->getUsers(),
            ];
        });
    }

    /**
     * Get roles for select dropdown
     */
    public function getRoles(): array
    {
        $cacheKey = $this->getCacheKey('select_options_roles');

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return Role::where('guard_name', 'web')
                ->orderBy('display_order')
                ->orderBy('name')
                ->get()
                ->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'label' => $role->label, // Translated label
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get permissions for select dropdown
     */
    public function getPermissions(): array
    {
        $cacheKey = $this->getCacheKey('select_options_permissions');

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return Permission::where('guard_name', 'web')
                ->orderBy('display_order')
                ->orderBy('name')
                ->get()
                ->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'label' => $permission->label ?? $permission->name,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get users for select dropdown (excluding admin users)
     */
    public function getUsers(): array
    {
        $cacheKey = $this->getCacheKey('select_options_users');

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $query = User::whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['teacher', 'assistant']);
            });

            $query->orderBy('display_order')->orderBy('name');

            return $query->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Clear all select options cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->getCacheKey('select_options_all'));
        Cache::forget($this->getCacheKey('select_options_roles'));
        Cache::forget($this->getCacheKey('select_options_permissions'));
        Cache::forget($this->getCacheKey('select_options_users'));
    }
}
