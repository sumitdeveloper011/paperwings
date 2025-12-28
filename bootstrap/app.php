<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'admin.auth' => \App\Http\Middleware\AdminAuthMiddleware::class,
            'prevent.admin' => \App\Http\Middleware\PreventAdminAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 404 errors - redirect based on user role and route type
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->is('admin/*')) {
                // Admin route 404
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $user = \Illuminate\Support\Facades\Auth::user();
                    if (\App\Helpers\CommonHelper::hasAnyRole($user, ['SuperAdmin', 'Admin'])) {
                        // Admin user trying to access non-existent admin route
                        try {
                            if (\Illuminate\Support\Facades\Route::has('admin.dashboard')) {
                                return redirect()->route('admin.dashboard')
                                    ->with('error', 'The requested admin page does not exist.');
                            }
                        } catch (\Exception $ex) {
                            // Route doesn't exist
                        }
                        return redirect()->route('admin.login')
                            ->with('error', 'The requested admin page does not exist.');
                    } else {
                        // Regular user trying to access admin route - redirect to home
                        return redirect()->route('home')
                            ->with('error', 'You do not have permission to access the admin panel.');
                    }
                } else {
                    // Guest trying to access admin route - redirect to admin login
                    return redirect()->route('admin.login')
                        ->with('error', 'Please login to access the admin panel.');
                }
            } else {
                // User route 404
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $user = \Illuminate\Support\Facades\Auth::user();
                    if (\App\Helpers\CommonHelper::hasAnyRole($user, ['SuperAdmin', 'Admin'])) {
                        // Admin user trying to access user route - redirect to admin panel
                        try {
                            if (\Illuminate\Support\Facades\Route::has('admin.dashboard')) {
                                return redirect()->route('admin.dashboard')
                                    ->with('error', 'Admin users cannot access user routes.');
                            }
                        } catch (\Exception $ex) {
                            // Route doesn't exist
                        }
                        return redirect()->route('admin.login')
                            ->with('error', 'Admin users cannot access user routes.');
                    }
                }
            }
            
            return null; // Let Laravel handle the 404 normally for other cases
        });
    })->create();
