<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Categories
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',

            // Products
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            // Orders
            'orders.view',
            'orders.edit',
            'orders.delete',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Roles & Permissions
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Settings
            'settings.view',
            'settings.edit',

            // Content Management
            'sliders.view',
            'sliders.create',
            'sliders.edit',
            'sliders.delete',
            'pages.view',
            'pages.create',
            'pages.edit',
            'pages.delete',
            'about-sections.view',
            'about-sections.create',
            'about-sections.edit',
            'about-sections.delete',

            // Marketing
            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',
            'testimonials.view',
            'testimonials.create',
            'testimonials.edit',
            'testimonials.delete',
            'special-offers.view',
            'special-offers.create',
            'special-offers.edit',
            'special-offers.delete',

            // Analytics
            'analytics.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $editorRole = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);

        // Assign all permissions to SuperAdmin
        $superAdminRole->syncPermissions(Permission::all());

        // Assign permissions to Admin (all except roles & permissions management)
        $adminPermissions = Permission::whereNotIn('name', [
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // Assign permissions to Manager (view and edit, no delete)
        $managerPermissions = Permission::where(function($query) {
            $query->where('name', 'LIKE', '%.view')
                  ->orWhere('name', 'LIKE', '%.edit')
                  ->orWhere('name', 'LIKE', 'dashboard.%');
        })->get();
        $managerRole->syncPermissions($managerPermissions);

        // Assign permissions to Editor (view only, limited edit)
        $editorPermissions = Permission::where(function($query) {
            $query->where('name', 'LIKE', '%.view')
                  ->orWhere('name', 'LIKE', 'dashboard.%')
                  ->orWhereIn('name', [
                      'products.edit',
                      'categories.edit',
                      'pages.edit',
                      'sliders.edit'
                  ]);
        })->get();
        $editorRole->syncPermissions($editorPermissions);

        // Assign SuperAdmin role to first user if exists
        $firstUser = User::first();
        if ($firstUser && !$firstUser->hasRole('SuperAdmin')) {
            $firstUser->assignRole('SuperAdmin');
        }

        $this->command->info('Roles and Permissions seeded successfully!');
        $this->command->info('Created ' . Permission::count() . ' permissions');
        $this->command->info('Created ' . Role::count() . ' roles');
    }
}

