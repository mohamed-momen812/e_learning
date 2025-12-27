<?php

namespace Modules\Admin\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
use Stancl\Tenancy\Database\Models\Tenant;

interface TenantRepositoryInterface extends RepositoryInterface
{
    /**
     * Find tenant by domain
     */
    public function findByDomain(string $domain): ?Tenant;
}
