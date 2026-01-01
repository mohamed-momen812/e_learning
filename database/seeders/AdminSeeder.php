<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            // Check if admin already exists
            $existingAdmin = User::on('central')
                ->where('email', 'admin@example.com')
                ->first();

            if ($existingAdmin) {
                $this->command->info('Super admin user already exists. Skipping...');
                return;
            }

            // Create super admin user
            $admin = User::on('central')->create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'), // Change this in production!
                'email_verified_at' => now(),
                'is_super_admin' => true,
            ]);

            $this->command->info('Super admin user created successfully!');
            $this->command->info('Email: admin@example.com');
            $this->command->warn('Password: password123 (Please change this in production!)');
        });
    }
}
