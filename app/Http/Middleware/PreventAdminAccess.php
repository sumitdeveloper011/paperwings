<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CommonHelper;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminAccess
{
    /**
     * Handle an incoming request.
     * Prevents admin users from accessing user/frontend routes
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user has admin role
            if (CommonHelper::hasAnyRole($user, ['SuperAdmin', 'Admin'])) {
                CommonHelper::logSecurityEvent('Admin attempted to access user route', $user, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'user_roles' => $user->roles->pluck('name')->toArray()
                ]);

                // Redirect admin to admin dashboard
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Admin users cannot access user routes. Please use the admin panel.');
            }
        }

        return $next($request);
    }
}

