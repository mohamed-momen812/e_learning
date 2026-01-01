<?php

namespace Modules\Admin\Services;

use App\Models\User;
use App\Core\Traits\HasDynamicOrdering;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUserService
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

        $query = User::query();

        // Exclude admin users (teacher and assistant roles)
        $query->whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['teacher']);
        });

        if (!empty($with)) {
            $query->with($with);
        }

        if (isset($filters['role'])) {
            $query->role($filters['role']);
        }

        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // Apply dynamic ordering
        $allowedSortFields = ['id', 'display_order', 'name', 'email', 'phone', 'created_at', 'updated_at'];
        $this->applyOrdering($query, $sort, $allowedSortFields, 'display_order');

        return $query->paginate($per_page, ['*'], 'page', $page);
    }
}

