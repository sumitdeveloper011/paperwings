<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    // Display a listing of roles
    public function index(Request $request): View|JsonResponse
    {
        $search = trim($request->get('search', ''));
        $query = Role::query();

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        $roles = $query->withCount('permissions', 'users')->orderBy('name')->paginate(15);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($roles->total() > 0 && $roles->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $roles->appends($request->query())
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.role.partials.table', compact('roles'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.role.index', compact('roles', 'search'));
    }

    // Show the form for creating a new role
    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'other';
        });

        return view('admin.role.create', compact('permissions'));
    }

    // Store a newly created role
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'uuid' => Str::uuid(),
                'name' => Str::slug($validated['name'], '_'),
                'guard_name' => $validated['guard_name'] ?? 'web',
            ]);

            if (!empty($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    // Display the specified role
    public function show(Role $role): View
    {
        $role->load('permissions', 'users');
        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'other';
        });

        return view('admin.role.show', compact('role', 'permissions'));
    }

    // Show the form for editing the specified role
    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'other';
        });

        $role->load('permissions');

        return view('admin.role.edit', compact('role', 'permissions'));
    }

    // Update the specified role
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'guard_name' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            $role->update([
                'name' => Str::slug($validated['name'], '_'),
                'guard_name' => $validated['guard_name'] ?? $role->guard_name,
            ]);

            if (isset($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    // Remove the specified role
    public function destroy(Role $role): RedirectResponse
    {
        try {
            // Prevent deletion of SuperAdmin role
            if ($role->name === 'SuperAdmin') {
                return redirect()->route('admin.roles.index')
                    ->with('error', 'SuperAdmin role cannot be deleted.');
            }

            // Check if role has users
            if ($role->users()->count() > 0) {
                return redirect()->route('admin.roles.index')
                    ->with('error', 'Cannot delete role. It is assigned to ' . $role->users()->count() . ' user(s).');
            }

            $role->delete();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }
}

