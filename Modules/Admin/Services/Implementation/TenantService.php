<?php

namespace Modules\Admin\Services\Implementation;

use App\Models\Tenant;  // Changed from Stancl\Tenancy\Database\Models\Tenant
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\DTOs\CreateTenantDTO;
use Modules\Admin\DTOs\TenantDTO;
use Modules\Admin\Repositories\Contracts\TenantRepositoryInterface;
use Modules\Admin\Services\Contracts\TenantServiceInterface;

class TenantService implements TenantServiceInterface
{
    protected TenantRepositoryInterface $repository;

    public function __construct(TenantRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new tenant
     */
    public function create(CreateTenantDTO $dto): TenantDTO
    {
        return DB::connection('central')->transaction(function () use ($dto) {
            // Create tenant using tenancy package
            $tenant = Tenant::create([
                'name' => $dto->name,  // Changed: set name directly instead of in data
                'data' => [],  // Keep data column for future use
            ]);

            // Create domain for the tenant
            $tenant->domains()->create([
                'domain' => $dto->domain,
            ]);

            // The TenantCreated event will automatically:
            // 1. Create the tenant database
            // 2. Run migrations on the tenant database

            return TenantDTO::fromModel($tenant->fresh(['domains']));
        });
    }

    /**
     * Update tenant
     */
    public function update(string $id, CreateTenantDTO $dto): TenantDTO
    {
        return DB::connection('central')->transaction(function () use ($id, $dto) {
            $tenant = $this->repository->findOrFail($id);

            // Update tenant name directly
            if (isset($dto->name)) {
                $tenant->update(['name' => $dto->name]);
            }

            // Update domain if provided
            if (isset($dto->domain)) {
                $domain = $tenant->domains->first();
                if ($domain) {
                    $domain->update(['domain' => $dto->domain]);
                } else {
                    $tenant->domains()->create(['domain' => $dto->domain]);
                }
            }

            return TenantDTO::fromModel($tenant->fresh(['domains']));
        });
    }

    /**
     * Delete tenant
     */
    public function delete(string $id): bool
    {
        // Note: The TenantDeleted event will automatically delete the database
        $tenant = $this->repository->findOrFail($id);
        return $this->repository->delete($tenant);
    }

    /**
     * Find tenant by ID
     */
    public function find(string $id): ?Tenant
    {
        return $this->repository->find($id);
    }

    /**
     * Find or fail
     */
    public function findOrFail(string $id): Tenant
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Get all tenants with pagination
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }
}
