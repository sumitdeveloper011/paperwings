<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = User::withCount(['wishlists', 'addresses'])
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['SuperAdmin', 'Admin']);
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
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.user.partials.table', compact('users'))->render(),
                'pagination' => view('admin.user.partials.pagination', compact('users'))->render()
            ]);
        }

        return view('admin.user.index', compact('users', 'search', 'status'));
    }

    /**
     * Display the specified user
     */
    public function show(User $user): View
    {
        // Prevent viewing admin users
        if ($user->hasAnyRole(['SuperAdmin', 'Admin'])) {
            abort(403, 'Cannot view admin users');
        }

        $user->load(['userDetail', 'addresses.region', 'wishlists.product']);
        
        // Get user orders
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.user.show', compact('user', 'orders'));
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        // Prevent updating admin users
        if ($user->hasAnyRole(['SuperAdmin', 'Admin'])) {
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
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User status updated successfully.');
    }

    /**
     * Delete user
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting admin users
        if ($user->hasAnyRole(['SuperAdmin', 'Admin'])) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete admin users.');
        }

        $userId = $user->id;
        $userEmail = $user->email;

        $user->delete();

        Log::info('User deleted', [
            'user_id' => $userId,
            'email' => $userEmail,
            'deleted_by' => auth()->id()
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}

