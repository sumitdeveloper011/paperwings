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
        try {
            if (Auth::check()) {
                $user = Auth::user();

                // Block only Admin and SuperAdmin users from accessing frontend routes
                try {
                    // Check if user has admin roles safely
                    if ($user && method_exists($user, 'roles')) {
                        // Only block if user has Admin or SuperAdmin role
                        if (CommonHelper::hasAnyRole($user, ['Admin', 'SuperAdmin'])) {
                            $userRoles = $user->roles->pluck('name')->toArray();

                            try {
                                CommonHelper::logSecurityEvent('Admin user attempted to access frontend route', $user, [
                                    'ip_address' => $request->ip(),
                                    'user_agent' => $request->userAgent(),
                                    'url' => $request->fullUrl(),
                                    'user_roles' => $userRoles
                                ]);
                            } catch (\Exception $e) {
                                // Log error but continue
                                \Log::warning('Failed to log security event: ' . $e->getMessage());
                            }

                            try {
                                if (\Illuminate\Support\Facades\Route::has('admin.dashboard')) {
                                    return redirect()->route('admin.dashboard')
                                        ->with('error', 'Admin users cannot access frontend. Please use the admin panel.');
                                }
                            } catch (\Exception $e) {
                                // Continue to admin.login redirect
                            }

                            return redirect()->route('admin.login')
                                ->with('error', 'Admin users cannot access frontend. Please use the admin panel.');
                        }
                    }
                } catch (\Exception $e) {
                    // If role check fails, log and continue (don't block the request)
                    \Log::warning('Role check failed in PreventAdminAccess middleware: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // If middleware fails completely, log and allow request to continue
            \Log::error('PreventAdminAccess middleware error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return $next($request);
    }
}

