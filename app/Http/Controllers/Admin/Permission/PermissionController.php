<?php

namespace App\Http\Controllers\Admin\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    // Display a listing of permissions
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $query = Permission::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $permissions = $query->withCount('roles')->orderBy('name')->paginate(20);

        // Group permissions by module
        $groupedPermissions = $permissions->getCollection()->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'other';
        });

        return view('admin.permission.index', compact('permissions', 'groupedPermissions', 'search'));
    }

    // Show the form for creating a new permission
    public function create(): View
    {
        return view('admin.permission.create');
    }

    // Store a newly created permission
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'nullable|string|max:255',
        ]);

        try {
            Permission::create([
                'name' => Str::slug($validated['name'], '_'),
                'guard_name' => $validated['guard_name'] ?? 'web',
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create permission: ' . $e->getMessage());
        }
    }

    // Display the specified permission
    public function show(Permission $permission): View
    {
        $permission->load('roles');
        return view('admin.permission.show', compact('permission'));
    }

    // Show the form for editing the specified permission
    public function edit(Permission $permission): View
    {
        return view('admin.permission.edit', compact('permission'));
    }

    // Update the specified permission
    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'guard_name' => 'nullable|string|max:255',
        ]);

        try {
            $permission->update([
                'name' => Str::slug($validated['name'], '_'),
                'guard_name' => $validated['guard_name'] ?? $permission->guard_name,
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update permission: ' . $e->getMessage());
        }
    }

    // Remove the specified permission
    public function destroy(Permission $permission): RedirectResponse
    {
        try {
            // Check if permission is assigned to any roles
            if ($permission->roles()->count() > 0) {
                return redirect()->route('admin.permissions.index')
                    ->with('error', 'Cannot delete permission. It is assigned to ' . $permission->roles()->count() . ' role(s).');
            }

            $permission->delete();

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Failed to delete permission: ' . $e->getMessage());
        }
    }
}

