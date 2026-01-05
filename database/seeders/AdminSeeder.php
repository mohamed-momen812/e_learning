<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds super admin user in central database.
     */
    public function run(): void
    {
        // Use central database connection
        DB::connection('central')->transaction(function () {
            // Check if superadmin already exists
            $existingAdmin = User::on('central')
                ->where('email', 'momen@mawsoaa.com')
                ->first();

            if ($existingAdmin) {
                $this->command->info('Super admin user already exists. Skipping...');
                return;
            }

            // Create super admin user
            $admin = User::on('central')->create([
                'name' => 'Momen',
                'email' => 'momen@mawsoaa.com',
                'password' => Hash::make('mawsoaa@0005000'), // Change this in production!
                'email_verified_at' => now(),
                'is_super_admin' => true,
            ]);

            // Assign superadmin role
            $superAdminRole = Role::where('name', 'super-admin')->first();
            $superAdminRole->givePermissionTo([
                'tenants.create',
                'tenants.view',
                'tenants.update',
                'tenants.delete',
                'tenants.bulk_delete',
                'tenants.update_order',
            ]);
            $superAdminRole->givePermissionTo([
                'users.create',
                'users.view',
                'users.update',
                'users.delete',
                'users.bulk_delete',
                'users.update_order',
                'users.manage_permissions',
            ]);
            $superAdminRole->givePermissionTo([
                'roles.create',
                'roles.view',
                'roles.update',
                'roles.delete',
                'roles.bulk_delete',
                'roles.update_order',
            ]);
            $superAdminRole->givePermissionTo([
                'permissions.view',
                'permissions.update_order',
                'permissions.bulk_delete',
            ]);
            $superAdminRole->givePermissionTo([
                'courses.create',
                'courses.view',
                'courses.update',
                'courses.delete',
                'courses.bulk_delete',
                'courses.update_order',
            ]);

            $superAdminRole->givePermissionTo([
                'lessons.create',
                'lessons.view',
                'lessons.update',
                'lessons.delete',
                'lessons.bulk_delete',
                'lessons.update_order',
            ]);

            $superAdminRole->givePermissionTo([
                'students.create',
                'students.view',
                'students.update',
                'students.delete',
                'students.bulk_delete',
                'students.update_order',
            ]);
            $superAdminRole->givePermissionTo([
                'enrollments.create',
                'enrollments.view',
                'enrollments.update',
                'enrollments.delete',
                'enrollments.bulk_delete',
                'enrollments.update_order',
            ]);
            $superAdminRole->givePermissionTo([
                'exams.create',
                'exams.view',
                'exams.update',
                'exams.delete',
                'exams.bulk_delete',
                'exams.update_order',
            ]);
            $superAdminRole->givePermissionTo([
                'attendance.view',
                'attendance.mark',
                'attendance.bulk_delete',
                'attendance.update_order',
            ]);
            $superAdminRole->givePermissionTo([
                'reports.view',
                'reports.generate',
                'reports.bulk_delete',
                'reports.update_order',
            ]);
            $superAdminRole->givePermissionTo([
                'permissions.view',
                'permissions.update_order',
                'permissions.bulk_delete',
            ]);

            if ($superAdminRole) {
                $admin->assignRole($superAdminRole);
            }

            $this->command->info('Super admin user created successfully!');
            $this->command->info('Super admin user role: super-admin');
            $this->command->info('Super admin user permissions: ' . implode(', ', $admin->getPermissionsViaRoles()->pluck('name')->toArray()));
            $this->command->info('Email: momen@mawsoaa.com');
            $this->command->warn('Password: mawsoaa@0005000 (Please change this in production!)');
        });
    }
}
