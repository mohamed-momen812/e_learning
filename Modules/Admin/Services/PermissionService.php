<?php

namespace Modules\Admin\Services;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class PermissionService
{
    /**
     * Find permission by ID
     */
    public function find(string $id): ?Model
    {
        return Permission::where('guard_name', 'web')->find($id);
    }

    /**
     * Find or fail
     */
    public function findOrFail(string $id): Model
    {
        return Permission::where('guard_name', 'web')->findOrFail($id);
    }
}

