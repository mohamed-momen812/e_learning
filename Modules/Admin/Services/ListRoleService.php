<?php

namespace Modules\Admin\Services;

use App\Core\Traits\HasDynamicOrdering;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class ListRoleService
{
    use HasDynamicOrdering;

    /**
     * Handle list request
     */
    public function handle(array $params): LengthAwarePaginator
    {
        $with = $params['with'] ?? [];
        $filters = $params['filters'] ?? [];
        $search = $params['search'] ?? '';
        $sort = $params['sort'] ?? 'display_order';
        $per_page = $params['per_page'] ?? 15;
        $page = $params['page'] ?? 1;

        $query = Role::query()->where('guard_name', 'web');

        if (! empty($with)) {
            $query->with($with);
        }

        // Filter by name
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        // Filter by permission(s)
        if (isset($filters['permission'])) {
            $permission = $filters['permission'];
            if (is_array($permission)) {
                // Multiple permissions: roles that have ANY of these permissions
                $query->whereHas('permissions', function ($q) use ($permission) {
                    $q->whereIn('name', $permission);
                });
            } else {
                // Single permission
                $query->whereHas('permissions', function ($q) use ($permission) {
                    $q->where('name', $permission);
                });
            }
        }

        // Search
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                // Search in label JSON field for all supported locales (en, ar)
                $q->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(label, "$.en")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(label, "$.ar")) LIKE ?', ['%' . $search . '%']);
            });
        }

        // Apply dynamic ordering
        $allowedSortFields = ['id', 'display_order', 'name', 'created_at', 'updated_at'];
        $this->applyOrdering($query, $sort, $allowedSortFields, 'display_order');

        return $query->paginate($per_page, ['*'], 'page', $page);
    }
}
