<?php

namespace Modules\Admin\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class ListRoleService
{
    /**
     * Handle list request
     */
    public function handle(array $params): LengthAwarePaginator
    {
        $with = $params['with'] ?? [];
        $filters = $params['filters'] ?? [];
        $search = $params['search'] ?? '';
        $sort = $params['sort'] ?? 'name';
        $per_page = $params['per_page'] ?? 15;
        $page = $params['page'] ?? 1;

        $query = Role::query()->where('guard_name', 'web');

        if (!empty($with)) {
            $query->with($with);
        }

        // Filter by name
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // Search
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Sort
        $sortField = ltrim($sort, '-');
        $sortDirection = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($per_page, ['*'], 'page', $page);
    }
}

