<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateTeacherUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates teacher user from tenant's data field.
     */
    public function run(): void
    {
        // Get current tenant
        $tenant = tenant();

        if (!$tenant) {
            return;
        }

        // Get teacher_user data from tenant's data field in central database
        $tenantFromCentral = Tenant::on('central')->find($tenant->id);

        if (!$tenantFromCentral) {
            return;
        }

        // Create the user in the tenant database
        $user = User::create([
            'name' => $tenantFromCentral->name,
            'email' => $tenantFromCentral->email,
            'password' => $tenantFromCentral->password,
            'phone' => $tenantFromCentral->phone ?? null,
            'email_verified_at' => now(),
        ]);

        // Assign teacher role
        $teacherRole = Role::where('name', 'teacher')->first();
        if ($teacherRole) {
            $user->assignRole($teacherRole);
        }
    }
}
