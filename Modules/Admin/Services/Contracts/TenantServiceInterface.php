<?php

namespace Modules\Admin\Services\Contracts;

use App\Models\Tenant;  // Changed from Stancl\Tenancy\Database\Models\Tenant
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\DTOs\CreateTenantDTO;
use Modules\Admin\DTOs\TenantDTO;

interface TenantServiceInterface
{
    /**
     * Create a new tenant
     */
    public function create(CreateTenantDTO $dto): TenantDTO;

    /**
     * Update tenant
     */
    public function update(string $id, CreateTenantDTO $dto): TenantDTO;

    /**
     * Delete tenant
     */
    public function delete(string $id): bool;

    /**
     * Find tenant by ID
     */
    public function find(string $id): ?Tenant;

    /**
     * Find or fail
     */
    public function findOrFail(string $id): Tenant;

    /**
     * Get all tenants with pagination
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
