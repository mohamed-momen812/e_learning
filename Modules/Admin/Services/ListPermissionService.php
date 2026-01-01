<?php

namespace Modules\Admin\Services;

use Spatie\Permission\Models\Permission;
use App\Core\Traits\HasDynamicOrdering;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPermissionService
{
    use HasDynamicOrdering;
    /**
     * Handle list request
     */
    public function handle(array $params): LengthAwarePaginator
    {
        $filters = $params['filters'] ?? [];
        $search = $params['search'] ?? '';
        $sort = $params['sort'] ?? 'display_order';
        $per_page = $params['per_page'] ?? 50;
        $page = $params['page'] ?? 1;

        $query = Permission::query()->where('guard_name', 'web');

        // Filter by name
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // Search
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Apply dynamic ordering
        $allowedSortFields = ['id', 'display_order', 'name', 'created_at', 'updated_at'];
        $this->applyOrdering($query, $sort, $allowedSortFields, 'display_order');

        return $query->paginate($per_page, ['*'], 'page', $page);
    }
}

