<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CommonHelper;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    // Handle an incoming request
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            CommonHelper::logSecurityEvent('Unauthenticated admin access attempt', null, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Please login to access the admin panel.'
            ]);
        }

        $user = Auth::user();

        if (!$user->isActive()) {
            Auth::logout();

            CommonHelper::logSecurityEvent('Inactive user attempted admin access', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl()
            ]);

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact administrator.'
            ]);
        }

        // Allow SuperAdmin, Admin, Manager, and Editor roles to access admin panel
        if (!CommonHelper::hasAnyRole($user, ['SuperAdmin', 'Admin', 'Manager', 'Editor'])) {
            CommonHelper::logSecurityEvent('Unauthorized admin access attempt', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'user_roles' => $user->roles->pluck('name')->toArray()
            ]);

            return redirect()->route('home')
                ->with('error', 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
