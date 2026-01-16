<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates all roles and permissions.
     * It will NOT run in production environment for safety.
     */
    public function run(): void
    {
        // Prevent running in production
        if (app()->environment('production')) {
            $this->command->warn('⚠️  RolePermissionSeeder skipped: Cannot run in production environment!');
            return;
        }

        $this->command->info('🌱 Seeding roles and permissions...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $this->createPermissions();

        // Create Roles
        $this->createRoles();

        // Assign Permissions to Roles
        $this->assignPermissionsToRoles();

        $this->command->info('✅ Roles and Permissions seeded successfully!');
        $this->command->info('  • Created ' . Permission::count() . ' permissions');
        $this->command->info('  • Created ' . Role::count() . ' roles');
    }

    /**
     * Create all permissions
     */
    private function createPermissions(): void
    {
        $this->command->info('Creating permissions...');

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
            'email-templates.view',
            'email-templates.create',
            'email-templates.edit',
            'email-templates.delete',
            'galleries.view',
            'galleries.create',
            'galleries.edit',
            'galleries.delete',
            'gallery-items.upload',
            'gallery-items.edit',
            'gallery-items.delete',

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
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['uuid' => Str::uuid()]
            );
        }

        $this->command->info('  ✓ Created ' . count($permissions) . ' permissions');
    }

    /**
     * Create all roles
     */
    private function createRoles(): void
    {
        $this->command->info('Creating roles...');

        $roles = [
            ['name' => 'SuperAdmin', 'guard_name' => 'web'],
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'Manager', 'guard_name' => 'web'],
            ['name' => 'Editor', 'guard_name' => 'web'],
            ['name' => 'User', 'guard_name' => 'web'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                ['uuid' => Str::uuid()]
            );
        }

        $this->command->info('  ✓ Created ' . count($roles) . ' roles');
    }

    /**
     * Assign permissions to roles
     */
    private function assignPermissionsToRoles(): void
    {
        $this->command->info('Assigning permissions to roles...');

        // Get roles
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $editorRole = Role::where('name', 'Editor')->first();
        $userRole = Role::where('name', 'User')->first();

        // Assign all permissions to SuperAdmin
        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::all());
            $this->command->info('  ✓ SuperAdmin: All permissions');
        }

        // Assign permissions to Admin (all except roles & permissions management)
        if ($adminRole) {
            $adminPermissions = Permission::whereNotIn('name', [
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'
            ])->get();
            $adminRole->syncPermissions($adminPermissions);
            $this->command->info('  ✓ Admin: ' . $adminPermissions->count() . ' permissions');
        }

        // Assign permissions to Manager (view and edit, no delete)
        if ($managerRole) {
            $managerPermissions = Permission::where(function($query) {
                $query->where('name', 'LIKE', '%.view')
                      ->orWhere('name', 'LIKE', '%.edit')
                      ->orWhere('name', 'LIKE', 'dashboard.%');
            })->get();
            $managerRole->syncPermissions($managerPermissions);
            $this->command->info('  ✓ Manager: ' . $managerPermissions->count() . ' permissions');
        }

        // Assign permissions to Editor (view only, limited edit)
        if ($editorRole) {
            $editorPermissions = Permission::where(function($query) {
                $query->where('name', 'LIKE', '%.view')
                      ->orWhere('name', 'LIKE', 'dashboard.%')
                      ->orWhereIn('name', [
                          'products.edit',
                          'categories.edit',
                          'pages.edit',
                          'sliders.edit',
                          'email-templates.view',
                          'galleries.create',
                          'galleries.edit',
                          'gallery-items.upload',
                          'gallery-items.edit'
                      ]);
            })->get();
            $editorRole->syncPermissions($editorPermissions);
            $this->command->info('  ✓ Editor: ' . $editorPermissions->count() . ' permissions');
        }

        // User role has no permissions (regular customer)
        if ($userRole) {
            $userRole->syncPermissions([]);
            $this->command->info('  ✓ User: No admin permissions (customer role)');
        }
    }
}
