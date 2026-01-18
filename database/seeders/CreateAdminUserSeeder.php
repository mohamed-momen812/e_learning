<?php

namespace Database\Seeders;

use Modules\Tenants\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates admin user from tenant's data field.
     */
    public function run(): void
    {
        // Get current tenant
        $tenant = tenant();

        if (! $tenant) {
            return;
        }

        // Get admin user data from tenant's data field in central database
        $tenantFromCentral = Tenant::on('central')->find($tenant->id);

        if (! $tenantFromCentral) {
            return;
        }

        // Create the user in the tenant database
        $user = User::create([
            'name' => $tenantFromCentral->name,
            'email' => $tenantFromCentral->email,
            'password' => $tenantFromCentral->password,
            'phone' => $tenantFromCentral->phone ?? null,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $user->assignRole($adminRole);
        }
    }
}
