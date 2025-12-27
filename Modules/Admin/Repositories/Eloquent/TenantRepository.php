<?php

namespace Modules\Admin\Repositories\Eloquent;

use App\Models\Tenant;  // Changed from Stancl\Tenancy\Database\Models\Tenant
use App\Repositories\Eloquent\BaseRepository;
use Modules\Admin\Repositories\Contracts\TenantRepositoryInterface;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
        // Ensure we're using the central connection
        $this->model->setConnection('central');
    }

    /**
     * Find tenant by domain
     */
    public function findByDomain(string $domain): ?Tenant
    {
        return $this->model
            ->whereHas('domains', function ($query) use ($domain) {
                $query->where('domain', $domain);
            })
            ->first();
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (isset($filters['name'])) {
            $query->whereJsonContains('data->name', $filters['name']);
        }

        if (isset($filters['domain'])) {
            $query->whereHas('domains', function ($q) use ($filters) {
                $q->where('domain', 'like', '%' . $filters['domain'] . '%');
            });
        }
    }
}
