<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permissions
        Permission::create(['name' => 'categories.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'categories.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'categories.edit', 'guard_name' => 'web']);
        Permission::create(['name' => 'categories.delete', 'guard_name' => 'web']);
        Permission::create(['name' => 'products.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'products.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);

        // Create roles
        $editorRole = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'Manager', 'guard_name' => 'web']);

        // Assign permissions to roles
        $editorRole->givePermissionTo(['categories.view', 'products.view', 'users.view', 'dashboard.view']);
        $managerRole->givePermissionTo([
            'categories.view', 'categories.create', 'categories.edit',
            'products.view', 'products.create',
            'users.view', 'users.create',
            'dashboard.view'
        ]);
    }

    /** @test */
    public function editor_can_see_menu_items_with_view_permission()
    {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');

        $this->actingAs($editor)
            ->get(route('admin.dashboard'))
            ->assertSee('Dashboard')
            ->assertSee('Categories')
            ->assertSee('Products')
            ->assertSee('Users');
    }

    /** @test */
    public function editor_cannot_see_create_button_without_create_permission()
    {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');

        $this->actingAs($editor)
            ->get(route('admin.categories.index'))
            ->assertDontSee('Add Category');
    }

    /** @test */
    public function manager_can_see_create_button_with_create_permission()
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $this->actingAs($manager)
            ->get(route('admin.categories.index'))
            ->assertSee('Add Category');
    }

    /** @test */
    public function editor_cannot_see_edit_button_without_edit_permission()
    {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');

        // Assuming we have a category in database
        $category = \App\Models\Category::factory()->create();

        $response = $this->actingAs($editor)
            ->get(route('admin.categories.index'));

        // Check that edit button is not visible
        $response->assertDontSee(route('admin.categories.edit', $category));
    }

    /** @test */
    public function editor_cannot_see_delete_button_without_delete_permission()
    {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');

        $category = \App\Models\Category::factory()->create();

        $response = $this->actingAs($editor)
            ->get(route('admin.categories.index'));

        // Check that delete form is not visible
        $response->assertDontSee(route('admin.categories.destroy', $category));
    }

    /** @test */
    public function manager_can_see_all_action_buttons_with_permissions()
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $category = \App\Models\Category::factory()->create();

        $response = $this->actingAs($manager)
            ->get(route('admin.categories.index'));

        $response->assertSee(route('admin.categories.show', $category))
                 ->assertSee(route('admin.categories.edit', $category))
                 ->assertSee('Add Category');
    }

    /** @test */
    public function sidebar_menu_hides_items_without_permission()
    {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');

        // Editor doesn't have orders.view permission
        $response = $this->actingAs($editor)
            ->get(route('admin.dashboard'));

        $response->assertDontSee('Orders & Customers');
    }

    /** @test */
    public function sidebar_menu_shows_items_with_permission()
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        // Manager has categories.view permission
        $response = $this->actingAs($manager)
            ->get(route('admin.dashboard'));

        $response->assertSee('Content Management')
                 ->assertSee('Categories');
    }

    /** @test */
    public function empty_state_create_button_respects_permissions()
    {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');

        // Editor doesn't have create permission
        $response = $this->actingAs($editor)
            ->get(route('admin.categories.index'));

        // If no categories exist, empty state should not show create button
        // This test assumes categories table is empty
        if (\App\Models\Category::count() === 0) {
            $response->assertDontSee('Add Category');
        }
    }

    /** @test */
    public function user_without_permission_gets_403_on_restricted_route()
    {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');

        // Editor doesn't have categories.create permission
        $this->actingAs($editor)
            ->get(route('admin.categories.create'))
            ->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_access_all_routes()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('SuperAdmin');

        // SuperAdmin should have all permissions
        $this->actingAs($superAdmin)
            ->get(route('admin.categories.create'))
            ->assertStatus(200);
    }

    /** @test */
    public function permission_checks_work_in_action_buttons()
    {
        $editor = User::factory()->create();
        $editor->assignRole('Editor');

        $category = \App\Models\Category::factory()->create();

        $response = $this->actingAs($editor)
            ->get(route('admin.categories.index'));

        // Editor can view but not edit/delete
        $response->assertSee(route('admin.categories.show', $category))
                 ->assertDontSee(route('admin.categories.edit', $category))
                 ->assertDontSee(route('admin.categories.destroy', $category));
    }
}
