<?php

namespace App\Http\Controllers\Admin\AdminUser;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Spatie\Activitylog\Models\Activity;

class AdminUserController extends Controller
{
    // Display a listing of admin users (SuperAdmin, Admin, Manager, Editor)
    public function index(Request $request): View|JsonResponse
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status');
        $roleFilter = $request->get('role');

        $query = User::withCount(['wishlists', 'addresses', 'orders'])
            ->with('roles.permissions')
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['SuperAdmin', 'Admin', 'Manager', 'Editor']);
            });

        // Filter by role if specified
        if ($roleFilter) {
            $query->whereHas('roles', function($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        if ($search !== '') {
            $searchTerm = trim($search);
            $query->where(function($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                  ->orWhere('last_name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchTerm}%"]);
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get admin roles for filter
        $roles = \Spatie\Permission\Models\Role::whereIn('name', ['SuperAdmin', 'Admin', 'Manager', 'Editor'])
            ->orderBy('name')
            ->get();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($users->total() > 0 && $users->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $users->appends($request->query())
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.admin-user.partials.table', compact('users'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.admin-user.index', compact('users', 'search', 'status', 'roleFilter', 'roles'));
    }

    // Show the form for creating a new admin user
    public function create(): View
    {
        // Only allow Admin, Manager, Editor roles (not SuperAdmin)
        $roles = \Spatie\Permission\Models\Role::whereIn('name', ['Admin', 'Manager', 'Editor'])
            ->orderBy('name')
            ->get();
        return view('admin.admin-user.create', compact('roles'));
    }

    // Store a newly created admin user
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:0,1',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ]);

        try {
            // Generate UUID first (will be used for folder name)
            $userUuid = \Illuminate\Support\Str::uuid()->toString();

            $userData = [
                'uuid' => $userUuid,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'status' => $validated['status'],
                'email_verified_at' => now(),
            ];

            // Upload avatar using ImageService
            if ($request->hasFile('avatar')) {
                $avatarPath = (new ImageService())->uploadImage($request->file('avatar'), 'users', $userUuid);
                if ($avatarPath) {
                    $userData['avatar'] = $avatarPath;
                }
            }

            $user = User::create($userData);

            // Assign roles
            if (!empty($validated['roles'])) {
                $roles = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->get();
                $user->syncRoles($roles);
            }

            // Manually log activity with better description (using admin_users log to distinguish from auto-log)
            activity()
                ->causedBy(Auth::user())
                ->performedOn($user)
                ->useLog('admin_users')
                ->withProperties([
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'status' => $user->status,
                ])
                ->log('Admin user created');

            // Send welcome email
            try {
                $user->notify(new \App\Notifications\WelcomeAdminUserNotification($validated['password']));
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email to admin user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }

            Log::info('Admin user created', [
                'user_id' => $user->id,
                'email' => $user->email,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('admin.admin-users.index')
                ->with('success', 'Admin user created successfully. Welcome email sent.');
        } catch (\Exception $e) {
            Log::error('Failed to create admin user', [
                'error' => $e->getMessage(),
                'created_by' => Auth::id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to create admin user: ' . $e->getMessage());
        }
    }

    // Display the specified admin user
    public function show(User $user): View
    {
        // Ensure user is an admin user
        if (!$user->hasAnyRole(['SuperAdmin', 'Admin', 'Manager', 'Editor'])) {
            abort(404);
        }

        $user->load(['userDetail', 'addresses.region', 'wishlists.product', 'roles'])
            ->loadCount(['wishlists', 'addresses', 'orders']);

        // Get activity logs for this user (only activities performed ON this user)
        // Filter duplicates: if "User created" and "Admin user created" exist at same time, show only "Admin user created"
        $allActivities = Activity::with(['causer.roles'])
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by time (same minute) and filter duplicates
        $grouped = $allActivities->groupBy(function($activity) {
            return $activity->created_at->format('Y-m-d H:i');
        });

        $activities = $grouped->map(function($group) {
            // If multiple activities in same minute, prefer admin_users log or "Admin user" description
            if ($group->count() > 1) {
                $adminUserActivity = $group->first(function($act) {
                    return $act->log_name === 'admin_users' ||
                           stripos(strtolower($act->description), 'admin user') !== false;
                });

                return $adminUserActivity ?: $group->first();
            }

            return $group->first();
        })->filter()->values()->take(50);

        return view('admin.admin-user.show', compact('user', 'activities'));
    }

    // Show the form for editing the specified admin user
    public function edit(User $user): View
    {
        // Ensure user is an admin user
        if (!$user->hasAnyRole(['SuperAdmin', 'Admin', 'Manager', 'Editor'])) {
            abort(404);
        }

        // Prevent editing SuperAdmin
        if ($user->hasRole('SuperAdmin') && !Auth::user()->hasRole('SuperAdmin')) {
            abort(403, 'Cannot edit SuperAdmin user.');
        }

        // Only allow Admin, Manager, Editor roles (not SuperAdmin)
        $roles = \Spatie\Permission\Models\Role::whereIn('name', ['Admin', 'Manager', 'Editor'])
            ->orderBy('name')
            ->get();
        $user->load('roles');
        return view('admin.admin-user.edit', compact('user', 'roles'));
    }

    // Update the specified admin user
    public function update(Request $request, User $user): RedirectResponse
    {
        // Ensure user is an admin user
        if (!$user->hasAnyRole(['SuperAdmin', 'Admin', 'Manager', 'Editor'])) {
            abort(404);
        }

        // Prevent editing SuperAdmin
        if ($user->hasRole('SuperAdmin') && !Auth::user()->hasRole('SuperAdmin')) {
            abort(403, 'Cannot edit SuperAdmin user.');
        }

        $oldValues = $user->only(['first_name', 'last_name', 'email', 'phone', 'status']);
        $oldRoles = $user->roles->pluck('name')->toArray();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => [
                'nullable',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
            'roles' => 'required|array|min:1',
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
            }

            // Log activity with changes
            $changes = [];
            foreach ($updateData as $key => $value) {
                if (isset($oldValues[$key]) && $oldValues[$key] != $value) {
                    $changes[$key] = [
                        'old' => $oldValues[$key],
                        'new' => $value
                    ];
                }
            }

            $newRoles = $user->roles->pluck('name')->toArray();
            if ($oldRoles != $newRoles) {
                $changes['roles'] = [
                    'old' => $oldRoles,
                    'new' => $newRoles
                ];
            }

            if (!empty($changes)) {
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($user)
                    ->withProperties([
                        'changes' => $changes,
                    ])
                    ->log('updated admin user');
            }

            Log::info('Admin user updated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('admin.admin-users.index')
                ->with('success', 'Admin user updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update admin user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'updated_by' => Auth::id()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update admin user: ' . $e->getMessage());
        }
    }

    // Update admin user status
    public function updateStatus(Request $request, User $user): RedirectResponse|JsonResponse
    {
        // Ensure user is an admin user
        if (!$user->hasAnyRole(['SuperAdmin', 'Admin', 'Manager', 'Editor'])) {
            abort(404);
        }

        // Prevent updating SuperAdmin status
        if ($user->hasRole('SuperAdmin')) {
            $message = 'Cannot update SuperAdmin user status.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->route('admin.admin-users.index')
                ->with('error', $message);
        }

        $validated = $request->validate([
            'status' => 'required|in:0,1'
        ]);

        // Cast status to integer
        $status = (int) $validated['status'];
        $oldStatus = $user->status;
        $user->update(['status' => $status]);

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $status,
            ])
            ->log('updated admin user status');

        Log::info('Admin user status updated', [
            'user_id' => $user->id,
            'status' => $status,
            'updated_by' => Auth::id()
        ]);

        $statusText = $status == 1 ? 'activated' : 'deactivated';
        $message = "Admin user {$statusText} successfully.";

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('admin.admin-users.index')
            ->with('success', $message);
    }

    // Delete admin user
    public function destroy(User $user): RedirectResponse
    {
        // Ensure user is an admin user
        if (!$user->hasAnyRole(['SuperAdmin', 'Admin', 'Manager', 'Editor'])) {
            abort(404);
        }

        // Prevent deleting SuperAdmin
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('admin.admin-users.index')
                ->with('error', 'Cannot delete SuperAdmin user.');
        }

        $userId = $user->id;
        $userEmail = $user->email;

        // Log activity before deletion
        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->withProperties([
                'email' => $userEmail,
                'roles' => $user->roles->pluck('name')->toArray(),
            ])
            ->log('deleted admin user');

        $user->delete();

        Log::info('Admin user deleted', [
            'user_id' => $userId,
            'email' => $userEmail,
            'deleted_by' => Auth::id()
        ]);

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'Admin user deleted successfully.');
    }
}
