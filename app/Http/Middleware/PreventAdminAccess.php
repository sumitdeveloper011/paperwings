<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CommonHelper;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminAccess
{
    // Handle an incoming request - prevents users with any role from accessing user/frontend routes
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Block any user who has ANY role (SuperAdmin, Admin, Manager, Editor, etc.)
            if ($user->roles && $user->roles->count() > 0) {
                $userRoles = $user->roles->pluck('name')->toArray();

                CommonHelper::logSecurityEvent('User with role attempted to access frontend route', $user, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'user_roles' => $userRoles
                ]);

                try {
                    if (\Illuminate\Support\Facades\Route::has('admin.dashboard')) {
                        return redirect()->route('admin.dashboard')
                            ->with('error', 'Users with roles cannot access frontend. Please use the admin panel.');
                    }
                } catch (\Exception $e) {
                }

                return redirect()->route('admin.login')
                    ->with('error', 'Users with roles cannot access frontend. Please use the admin panel.');
            }
        }

        return $next($request);
    }
}

