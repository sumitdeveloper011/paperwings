<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

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
        // Handle 419 - Page Expired (CSRF Token Mismatch)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session has expired. Please refresh the page and try again.',
                    'error' => 'CSRF token mismatch'
                ], 419);
            }

            if ($request->is('admin/*')) {
                // Don't show custom error page in development - show Laravel's debug page
                if (app()->environment('local', 'development')) {
                    return null;
                }
                return response()->view('admin.errors.419', ['exception' => $e], 419);
            }

            return response()->view('frontend.errors.419', [], 419);
        });

        // Handle 401 - Unauthenticated
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*') || $request->is('cart/*') || $request->is('wishlist/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to continue.',
                    'error' => 'Unauthenticated',
                    'redirect' => '/login?intended=' . urlencode($request->fullUrl())
                ], 401);
            }

            if ($request->is('admin/*')) {
                return redirect()->route('admin.login')
                    ->with('error', 'Please login to access the admin panel.');
            }

            return redirect()->route('login')
                ->with('error', 'Please login to continue.');
        });

        // Handle 403 - Forbidden
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have permission to access this resource.',
                    'error' => 'Access denied'
                ], 403);
            }

            if ($request->is('admin/*')) {
                return response()->view('admin.errors.403', ['exception' => $e], 403);
            }

            return response()->view('frontend.errors.403', [], 403);
        });

        // Handle ValidationException - Redirect back with validation errors
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }

            // For web requests, redirect back with errors
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors($e->errors());
        });

        // Handle 500 - Internal Server Error
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Don't log AuthenticationException or ValidationException as they're handled above
            if (!($e instanceof \Illuminate\Auth\AuthenticationException) &&
                !($e instanceof \Illuminate\Validation\ValidationException)) {
                // Log the error
                Log::error('Unhandled exception: ' . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => app()->environment('production')
                        ? 'An error occurred. Please try again later.'
                        : $e->getMessage(),
                    'error' => 'Internal server error'
                ], 500);
            }

            // Don't show custom error page in development - show Laravel's debug page
            if (app()->environment('local', 'development')) {
                return null;
            }

            if ($request->is('admin/*')) {
                return response()->view('admin.errors.500', [], 500);
            }

            return response()->view('frontend.errors.500', [], 500);
        });

        // Handle 404 errors - redirect based on user role and route type
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            // Ignore static asset requests (assets/*, favicon.ico, etc.) - return silent 404
            if ($request->is('assets/*') ||
                $request->is('*.js') ||
                $request->is('*.css') ||
                $request->is('*.ico') ||
                $request->is('*.png') ||
                $request->is('*.jpg') ||
                $request->is('*.jpeg') ||
                $request->is('*.gif') ||
                $request->is('*.svg') ||
                $request->is('*.woff') ||
                $request->is('*.woff2') ||
                $request->is('*.ttf') ||
                $request->is('*.eot')) {
                return response('', 404);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The requested resource was not found.',
                    'error' => 'Not found'
                ], 404);
            }

            if ($request->is('admin/*')) {
                // Admin route 404
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $user = \Illuminate\Support\Facades\Auth::user();
                    if (\App\Helpers\CommonHelper::hasAnyRole($user, ['SuperAdmin', 'Admin'])) {
                        // Admin user trying to access non-existent admin route - show 404 page
                        // Don't show custom error page in development - show Laravel's debug page
                        if (app()->environment('local', 'development')) {
                            return null;
                        }
                        return response()->view('admin.errors.404', [
                            'title' => '404 - Page Not Found',
                            'message' => 'The requested admin page does not exist.'
                        ], 404);
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
                // User route 404 - show custom 404 page
                return response()->view('frontend.errors.404', [
                    'title' => '404 - Page Not Found',
                    'message' => 'The page you are looking for does not exist.'
                ], 404);
            }
        });

        // Handle 503 - Service Unavailable (Maintenance Mode)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            // Check if it's a 503 error and app is in maintenance mode
            if ($e->getStatusCode() === 503 && app()->isDownForMaintenance()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Service is temporarily unavailable. We are performing maintenance.',
                        'error' => 'Service unavailable'
                    ], 503);
                }

                if ($request->is('admin/*')) {
                    // Don't show custom error page in development - show Laravel's debug page
                    if (app()->environment('local', 'development')) {
                        return null;
                    }
                    return response()->view('admin.errors.503', ['exception' => $e], 503);
                }

                return response()->view('frontend.errors.503', [], 503);
            }

            // Let other 503 errors be handled by default handler
            return null;
        });
    })->create();
