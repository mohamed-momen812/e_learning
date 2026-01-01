<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant database.
     * This seeder runs automatically when a tenant is created.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CreateTeacherUserSeeder::class,
        ]);
    }
}
