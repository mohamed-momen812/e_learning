<?php

namespace Modules\SuperAdmin\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TenantService
{
    /**
     * Create a new tenant
     */
    public function create(array $data): Model
    {
        return DB::connection('central')->transaction(function () use ($data) {
            $tenant = Tenant::create([
                'name' => $data['name'],
                'data' => [],
            ]);

            $tenant->domains()->create([
                'domain' => $data['domain'],
            ]);

            // The TenantCreated event will automatically:
            // 1. Create the tenant database
            // 2. Run migrations on the tenant database and seed the data

            return $tenant->fresh(['domains']);
        });
    }

    /**
     * Update tenant
     */
    public function update(string $id, array $data): Model
    {
        return DB::connection('central')->transaction(function () use ($id, $data) {
            $tenant = Tenant::findOrFail($id);

            if (isset($data['name'])) {
                $tenant->update(['name' => $data['name']]);
            }

            if (isset($data['domain'])) {
                $domain = $tenant->domains->first();
                if ($domain) {
                    $domain->update(['domain' => $data['domain']]);
                } else {
                    $tenant->domains()->create(['domain' => $data['domain']]);
                }
            }

            return $tenant->fresh(['domains']);
        });
    }

    /**
     * Delete tenant
     */
    public function delete(string $id): bool
    {
        // Note: The TenantDeleted event will automatically delete the database
        $tenant = Tenant::findOrFail($id);
        return $tenant->delete();
    }

    /**
     * Find tenant by ID
     */
    public function find(string $id): ?Model
    {
        return Tenant::with('domains')->find($id);
    }

    /**
     * Find or fail
     */
    public function findOrFail(string $id): Model
    {
        return Tenant::with('domains')->findOrFail($id);
    }
}
