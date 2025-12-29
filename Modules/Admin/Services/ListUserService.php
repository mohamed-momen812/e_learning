<?php

namespace Modules\Admin\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUserService
{
    /**
     * Handle list request
     */
    public function handle(array $params): LengthAwarePaginator
    {
        $with = $params['with'] ?? [];
        $filters = $params['filters'] ?? [];
        $search = $params['search'] ?? '';
        $sort = $params['sort'] ?? 'created_at';
        $per_page = $params['per_page'] ?? 15;
        $page = $params['page'] ?? 1;

        $query = User::query();

        if (!empty($with)) {
            $query->with($with);
        }

        // Filter by role
        if (isset($filters['role'])) {
            $query->role($filters['role']);
        }

        // Filter by email
        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // Sort
        $sortField = ltrim($sort, '-');
        $sortDirection = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $query->orderBy($sortField, $sortDirection);

        return $query->paginate($per_page, ['*'], 'page', $page);
    }
}

