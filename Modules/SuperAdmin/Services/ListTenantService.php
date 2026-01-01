<?php

namespace Modules\SuperAdmin\Services;

use App\Models\Tenant;
use App\Core\Traits\HasDynamicOrdering;
use Illuminate\Pagination\LengthAwarePaginator;

class ListTenantService
{
    use HasDynamicOrdering;
    /**
     * Handle list request
     */
    public function handle(array $params): LengthAwarePaginator
    {
        $with = $params['with'];
        $filters = $params['filters'];
        $search = $params['search'];
        $sort = $params['sort'] ?? 'display_order';
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

        // Apply dynamic ordering
        $allowedSortFields = ['id', 'display_order', 'name', 'email', 'is_active', 'created_at', 'updated_at'];
        $this->applyOrdering($query, $sort, $allowedSortFields, 'display_order');

        return $query->paginate($per_page, ['*'], 'page', $page);
    }
}
