<?php

namespace Modules\SuperAdmin\Services;

use App\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

class ListTenantService
{
    /**
     * Handle list request
     */
    public function handle(array $params): LengthAwarePaginator
    {
        $with = $params['with'];
        $filters = $params['filters'];
        $search = $params['search'];
        $sort = $params['sort'];
        $per_page = $params['per_page'];
        $page = $params['page'];

        $query = Tenant::query();

        if (!empty($with)) {
            $query->with($with);
        }

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $sortField = ltrim($sort, '-');
        $sortDirection = str_starts_with($sort, '-') ? 'desc' : 'asc';

        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($per_page, ['*'], 'page', $page);
    }
}
