<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // SuperAdmin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@paperwings.co.nz'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'Sheik',
                'last_name' => 'Ashaid',
                'email_verified_at' => now(),
                'password' => Hash::make('#Paperwings29waterloord'),
                'status' => 1, // Active
            ]
        );

        // Assign SuperAdmin role
        if (!$superAdmin->hasRole('SuperAdmin')) {
            $superAdmin->assignRole('SuperAdmin');
        }

        // Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'adminuser@paperwings.co.nz'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => 1, // Active
            ]
        );

        // Assign Admin role
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }

        // Manager User
        $managerUser = User::updateOrCreate(
            ['email' => 'manager@paperwings.co.nz'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => 1, // Active
            ]
        );

        // Assign Manager role
        if (!$managerUser->hasRole('Manager')) {
            $managerUser->assignRole('Manager');
        }

        $this->command->info('Admin users created successfully!');
        $this->command->info('SuperAdmin: admin@paperwings.co.nz');
        $this->command->info('Admin: adminuser@paperwings.co.nz (password: password)');
        $this->command->info('Manager: manager@paperwings.co.nz (password: password)');
    }
}
