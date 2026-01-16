<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Display a listing of users (only User role customers)
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = User::withCount(['wishlists', 'addresses', 'orders'])
            ->with('roles.permissions')
            ->whereHas('roles', function($q) {
                $q->where('name', 'User');
            });

        if ($search) {
            $searchTerm = trim($search);
            // Optimized search with better indexing support
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "{$searchTerm}%")
                  ->orWhere('last_name', 'like', "{$searchTerm}%")
                  ->orWhere('email', 'like', "{$searchTerm}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            return response()->json([
                'html' => view('admin.user.partials.table', compact('users'))->render(),
                'pagination' => view('admin.user.partials.pagination', compact('users'))->render()
            ]);
        }

        return view('admin.user.index', compact('users', 'search', 'status'));
    }

    // Show the form for creating a new user
    public function create(): View
    {
        // Exclude SuperAdmin and User roles from selection
        $roles = \Spatie\Permission\Models\Role::whereNotIn('name', ['SuperAdmin', 'User'])
            ->orderBy('name')
            ->get();
        return view('admin.user.create', compact('roles'));
    }

    // Store a newly created user
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'],
                'email_verified_at' => now(),
            ]);

            // Assign roles if provided
            if (!empty($validated['roles'])) {
                $roles = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->get();
                $user->syncRoles($roles);
            }

            Log::info('User created by admin', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'created_by' => Auth::id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    // Display the specified user
    public function show(User $user): View
    {
        $user->load(['userDetail', 'addresses.region', 'wishlists.product', 'roles'])
            ->loadCount(['wishlists', 'addresses', 'orders']);

        // Get user orders with items (load items first, then products)
        $orders = Order::where('user_id', $user->id)
            ->with(['items' => function($query) {
                $query->with('product');
            }])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.user.show', compact('user', 'orders'));
    }

    // Show the form for editing the specified user
    public function edit(User $user): View
    {
        // Exclude SuperAdmin and User roles from selection
        $roles = \Spatie\Permission\Models\Role::whereNotIn('name', ['SuperAdmin', 'User'])
            ->orderBy('name')
            ->get();
        $user->load('roles');
        return view('admin.user.edit', compact('user', 'roles'));
    }

    // Update the specified user
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            $updateData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'],
            ];

            // Update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Sync roles
            if (isset($validated['roles'])) {
                $roles = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->get();
                $user->syncRoles($roles);
            } else {
                $user->syncRoles([]);
            }

            Log::info('User updated by admin', [
                'user_id' => $user->id,
                'email' => $user->email,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'updated_by' => Auth::id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    // Update user status
    public function updateStatus(Request $request, User $user): RedirectResponse|JsonResponse
    {
        // Prevent updating admin users
        if ($user->hasAnyRole(['SuperAdmin', 'Admin'])) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update admin user status.'
                ], 403);
            }
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot update admin user status.');
        }

        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        $user->update(['status' => $request->status]);

        Log::info('User status updated', [
            'user_id' => $user->id,
            'status' => $request->status,
            'updated_by' => Auth::id()
        ]);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully.'
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User status updated successfully.');
    }

    // Delete user
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting SuperAdmin
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete SuperAdmin user.');
        }

        $userId = $user->id;
        $userEmail = $user->email;

        $user->delete();

        Log::info('User deleted', [
            'user_id' => $userId,
            'email' => $userEmail,
            'deleted_by' => Auth::id()
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}

