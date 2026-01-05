<?php

namespace Modules\SuperAdmin\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class TenantService
{
    /**
     * Create a new tenant
     */
    public function create(array $data): Model
    {
        return DB::connection('central')->transaction(function () use ($data) {
            // Get the next display_order value
            $maxOrder = Tenant::max('display_order') ?? 0;
            
            $tenant = Tenant::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'is_active' => $data['is_active'] ?? 1,
                'password' => Hash::make($data['password']),
                'display_order' => $maxOrder + 1,
            ]);

            // Create multiple domains
            $domains = $data['domains'] ?? [];
            foreach ($domains as $domain) {
                $tenant->domains()->create([
                    'domain' => $domain,
                ]);
            }

            // The TenantCreated event will automatically:
            // 1. Create the tenant database
            // 2. Run migrations on the tenant database and seed the data
            // 3. TenantDatabaseSeeder will create the teacher user

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

            // Update tenant basic info
            $updateData = [];
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (isset($data['email'])) {
                $updateData['email'] = $data['email'];
            }
            if (isset($data['phone'])) {
                $updateData['phone'] = $data['phone'];
            }
            if (isset($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = $data['is_active'];
            }

            if (!empty($updateData)) {
                $tenant->update($updateData);
            }

            // Handle domains update
            if (isset($data['domains'])) {
                $newDomains = $data['domains'];
                $existingDomains = $tenant->domains->pluck('domain')->toArray();

                // Get domains to delete (existing but not in new list)
                $domainsToDelete = array_diff($existingDomains, $newDomains);

                // Get domains to add (new but not in existing list)
                $domainsToAdd = array_diff($newDomains, $existingDomains);

                // Delete removed domains
                if (!empty($domainsToDelete)) {
                    $tenant->domains()->whereIn('domain', $domainsToDelete)->delete();
                }

                // Add new domains
                foreach ($domainsToAdd as $domain) {
                    $tenant->domains()->create(['domain' => $domain]);
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
     * Bulk delete tenants
     */
    public function bulkDelete(array $ids): array
    {
        $tenants = Tenant::whereIn('id', $ids)->get();

        $deleted = [];
        $skipped = [];

        foreach ($tenants as $tenant) {
            try {
                // Note: The TenantDeleted event will automatically delete the database
                $tenant->delete();
                $deleted[] = $tenant->id;
            } catch (\Exception $e) {
                $skipped[] = [
                    'id' => $tenant->id,
                    'reason' => $e->getMessage()
                ];
            }
        }

        return [
            'deleted' => $deleted,
            'skipped' => $skipped,
            'deleted_count' => count($deleted),
            'skipped_count' => count($skipped),
        ];
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
