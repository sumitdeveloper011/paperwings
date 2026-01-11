<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates both admin users and regular users.
     * It will NOT run in production environment for safety.
     */
    public function run(): void
    {
        // Prevent running in production
        if (app()->environment('production')) {
            $this->command->warn('⚠️  UserSeeder skipped: Cannot run in production environment!');
            return;
        }

        $this->command->info('🌱 Seeding users...');

        // Create Admin Users
        $this->createAdminUsers();

        // Create Regular Users
        $this->createRegularUsers();

        $this->command->info('✅ Users seeded successfully!');
    }

    /**
     * Create admin users (SuperAdmin, Admin, Manager, Editor)
     */
    private function createAdminUsers(): void
    {
        $this->command->info('Creating admin users...');

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
            ['email' => 'admin@example.com'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email_verified_at' => now(),
                'password' => Hash::make('Password123!'),
                'status' => 1, // Active
            ]
        );

        // Assign Admin role
        if (!$adminUser->hasRole('Admin')) {
            $adminUser->assignRole('Admin');
        }

        // Manager User
        $managerUser = User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email_verified_at' => now(),
                'password' => Hash::make('Password123!'),
                'status' => 1, // Active
            ]
        );

        // Assign Manager role
        if (!$managerUser->hasRole('Manager')) {
            $managerUser->assignRole('Manager');
        }

        // Editor User
        $editorUser = User::updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'Editor',
                'last_name' => 'User',
                'email_verified_at' => now(),
                'password' => Hash::make('Password123!'),
                'status' => 1, // Active
            ]
        );

        // Assign Editor role
        if (!$editorUser->hasRole('Editor')) {
            $editorUser->assignRole('Editor');
        }

        $this->command->info('Admin users created:');
        $this->command->line('  • SuperAdmin: admin@paperwings.co.nz');
        $this->command->line('  • Admin: admin@example.com (password: Password123!)');
        $this->command->line('  • Manager: manager@example.com (password: Password123!)');
        $this->command->line('  • Editor: editor@example.com (password: Password123!)');
    }

    /**
     * Create regular users (customers)
     */
    private function createRegularUsers(): void
    {
        $this->command->info('Creating regular users...');

        // Regular User
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'User',
                'last_name' => 'Singh',
                'email_verified_at' => now(),
                'password' => Hash::make('Password123!'),
                'status' => 1, // Active
            ]
        );

        // Assign User role
        if (!$user->hasRole('User')) {
            $user->assignRole('User');
        }

        $this->command->info('Regular user created:');
        $this->command->line('  • User: user@example.com (password: Password123!)');
    }
}
