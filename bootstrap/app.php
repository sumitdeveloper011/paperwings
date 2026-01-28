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
        
        // Add security headers to all web requests
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeadersMiddleware::class);
        
        $middleware->validateCsrfTokens(except: [
            'api/log-client-error',
            'stripe/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // ============================================
        // SPECIFIC EXCEPTION HANDLERS (Most Specific First)
        // ============================================

        // Handle 419 - Page Expired (CSRF Token Mismatch) - Specific Exception
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return \App\Helpers\JsonResponseHelper::error(
                    'Your session has expired. Please refresh the page and try again.',
                    'CSRF_TOKEN_MISMATCH',
                    null,
                    419
                );
            }

            if ($request->is('admin/*')) {
                try {
                    return response()->view('admin.errors.419', ['exception' => $e], 419);
                } catch (\Throwable $viewError) {
                    Log::error('Failed to render 419 error page: ' . $viewError->getMessage(), [
                        'trace' => $viewError->getTraceAsString()
                    ]);
                    return response('<h1>419 - Page Expired</h1><p>Your session has expired. Please <a href="' . route('admin.login') . '">refresh the page</a> and try again.</p>', 419)
                        ->header('Content-Type', 'text/html');
                }
            }

            try {
                return response()->view('frontend.errors.419', [], 419);
            } catch (\Throwable $viewError) {
                Log::error('Failed to render 419 error page: ' . $viewError->getMessage());
                return response('<h1>419 - Page Expired</h1><p>Your session has expired. Please refresh the page and try again.</p>', 419)
                    ->header('Content-Type', 'text/html');
            }
        });

        // Handle 401 - Unauthenticated
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->wantsJson() || $request->is('api/*') || $request->is('cart/*') || $request->is('wishlist/*')) {
                return \App\Helpers\JsonResponseHelper::error(
                    'Please login to continue.',
                    'UNAUTHENTICATED',
                    ['redirect' => '/login?intended=' . urlencode($request->fullUrl())],
                    401
                );
            }

            if ($request->is('admin/*')) {
                return redirect()->route('admin.login')
                    ->with('error', 'Please login to access the admin panel.');
            }

            return redirect()->route('login')
                ->with('error', 'Please login to continue.');
        });

        // Handle ValidationException - Redirect back with validation errors
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return \App\Helpers\JsonResponseHelper::validationError(
                    $e->errors(),
                    'The given data was invalid.'
                );
            }

            // For web requests, redirect back with errors
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors($e->errors());
        });

        // Handle 403 - Forbidden
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return \App\Helpers\JsonResponseHelper::error(
                    'You do not have permission to access this resource.',
                    'ACCESS_DENIED',
                    null,
                    403
                );
            }

            if ($request->is('admin/*')) {
                try {
                    return response()->view('admin.errors.403', ['exception' => $e], 403);
                } catch (\Throwable $viewError) {
                    Log::error('Failed to render 403 error page: ' . $viewError->getMessage());
                    return response('<h1>403 - Forbidden</h1><p>You do not have permission to access this resource.</p>', 403)
                        ->header('Content-Type', 'text/html');
                }
            }

            try {
                return response()->view('frontend.errors.403', [], 403);
            } catch (\Throwable $viewError) {
                Log::error('Failed to render 403 error page: ' . $viewError->getMessage());
                return response('<h1>403 - Forbidden</h1><p>You do not have permission to access this resource.</p>', 403)
                    ->header('Content-Type', 'text/html');
            }
        });

        // Handle 404 errors - redirect based on user role and route type
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            // Ignore static asset requests - return silent 404
            $staticExtensions = ['js', 'css', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'eot'];
            $path = $request->path();
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            
            if (in_array($extension, $staticExtensions) || $request->is('assets/*')) {
                return response('', 404);
            }

            if ($request->expectsJson()) {
                return \App\Helpers\JsonResponseHelper::error(
                    'The requested resource was not found.',
                    'NOT_FOUND',
                    null,
                    404
                );
            }

            if ($request->is('admin/*')) {
                // Admin route 404
                if (\Illuminate\Support\Facades\Auth::check()) {
                    $user = \Illuminate\Support\Facades\Auth::user();
                    if (\App\Helpers\CommonHelper::hasAnyRole($user, ['SuperAdmin', 'Admin'])) {
                        // Admin user trying to access non-existent admin route - show 404 page
                        try {
                            return response()->view('admin.errors.404', [
                                'title' => '404 - Page Not Found',
                                'message' => 'The requested admin page does not exist.'
                            ], 404);
                        } catch (\Throwable $viewError) {
                            Log::error('Failed to render 404 error page: ' . $viewError->getMessage());
                            return response('<h1>404 - Page Not Found</h1><p>The requested admin page does not exist.</p>', 404)
                                ->header('Content-Type', 'text/html');
                        }
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
                try {
                    return response()->view('frontend.errors.404', [
                        'title' => '404 - Page Not Found',
                        'message' => 'The page you are looking for does not exist.'
                    ], 404);
                } catch (\Throwable $viewError) {
                    Log::error('Failed to render 404 error page: ' . $viewError->getMessage());
                    return response('<!DOCTYPE html><html><head><title>404 - Not Found</title></head><body><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p></body></html>', 404)
                        ->header('Content-Type', 'text/html');
                }
            }
        });

        // ============================================
        // HTTP STATUS CODE HANDLERS (Consolidated)
        // ============================================

        // Consolidated HttpException handler for 419, 429, 503
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            $statusCode = $e->getStatusCode();

            // Handle 419 - Page Expired (fallback for HttpException with 419 status)
            if ($statusCode === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your session has expired. Please refresh the page and try again.',
                        'error' => 'CSRF token mismatch'
                    ], 419);
                }

                if ($request->is('admin/*')) {
                    try {
                        return response()->view('admin.errors.419', ['exception' => $e], 419);
                    } catch (\Throwable $viewError) {
                        Log::error('Failed to render 419 error page: ' . $viewError->getMessage());
                        return response('<h1>419 - Page Expired</h1><p>Your session has expired. Please <a href="' . route('admin.login') . '">refresh the page</a> and try again.</p>', 419)
                            ->header('Content-Type', 'text/html');
                    }
                }

                try {
                    return response()->view('frontend.errors.419', [], 419);
                } catch (\Throwable $viewError) {
                    Log::error('Failed to render 419 error page: ' . $viewError->getMessage());
                    return response('<h1>419 - Page Expired</h1><p>Your session has expired. Please refresh the page and try again.</p>', 419)
                        ->header('Content-Type', 'text/html');
                }
            }

            // Handle 429 - Too Many Requests (Rate Limiting)
            if ($statusCode === 429) {
                if ($request->expectsJson()) {
                    return \App\Helpers\JsonResponseHelper::error(
                        'Too many requests. Please wait a moment before trying again.',
                        'RATE_LIMIT_EXCEEDED',
                        ['retry_after' => $e->getHeaders()['Retry-After'] ?? 60],
                        429
                    );
                }

                if ($request->is('admin/*')) {
                    try {
                        return response()->view('admin.errors.429', ['exception' => $e], 429);
                    } catch (\Throwable $viewError) {
                        Log::error('Failed to render 429 error page: ' . $viewError->getMessage());
                        return response('<h1>429 - Too Many Requests</h1><p>Please wait a moment before trying again.</p>', 429)
                            ->header('Content-Type', 'text/html');
                    }
                }

                try {
                    return response()->view('frontend.errors.429', ['exception' => $e], 429);
                } catch (\Throwable $viewError) {
                    Log::error('Failed to render 429 error page: ' . $viewError->getMessage());
                    return response('<h1>429 - Too Many Requests</h1><p>Please wait a moment before trying again.</p>', 429)
                        ->header('Content-Type', 'text/html');
                }
            }

            // Handle 503 - Service Unavailable (Maintenance Mode)
            if ($statusCode === 503 && app()->isDownForMaintenance()) {
                if ($request->expectsJson()) {
                    return \App\Helpers\JsonResponseHelper::error(
                        'Service is temporarily unavailable. We are performing maintenance.',
                        'SERVICE_UNAVAILABLE',
                        null,
                        503
                    );
                }

                if ($request->is('admin/*')) {
                    // Don't show custom error page in development - show Laravel's debug page
                    if (app()->environment('local', 'development')) {
                        return null;
                    }
                    try {
                        return response()->view('admin.errors.503', ['exception' => $e], 503);
                    } catch (\Throwable $viewError) {
                        Log::error('Failed to render 503 error page: ' . $viewError->getMessage());
                        return response('<h1>503 - Service Unavailable</h1><p>We are performing maintenance. Please check back soon.</p>', 503)
                            ->header('Content-Type', 'text/html');
                    }
                }

                try {
                    return response()->view('frontend.errors.503', [], 503);
                } catch (\Throwable $viewError) {
                    Log::error('Failed to render 503 error page: ' . $viewError->getMessage());
                    return response('<h1>503 - Service Unavailable</h1><p>We are performing maintenance. Please check back soon.</p>', 503)
                        ->header('Content-Type', 'text/html');
                }
            }

            // Let other HttpExceptions be handled by default handler
            return null;
        });

        // ============================================
        // CATCH-ALL HANDLER (Least Specific - Last)
        // ============================================

        // Handle 500 - Internal Server Error (Catch-all for unhandled exceptions)
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Skip exceptions that are already handled by specific handlers above
            if ($e instanceof \Illuminate\Auth\AuthenticationException ||
                $e instanceof \Illuminate\Validation\ValidationException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException ||
                $e instanceof \Illuminate\Session\TokenMismatchException) {
                return null; // Let other handlers process these
            }

            // Skip HttpExceptions with status codes handled above
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $statusCode = $e->getStatusCode();
                if (in_array($statusCode, [419, 429, 503])) {
                    return null; // Let specific HttpException handler process these
                }
            }

            // Special logging for API error endpoint
            if ($request->is('api/log-client-error')) {
                Log::error('=== EXCEPTION in api/log-client-error route ===', [
                    'exception_message' => $e->getMessage(),
                    'exception_class' => get_class($e),
                    'exception_code' => $e->getCode(),
                    'exception_file' => $e->getFile(),
                    'exception_line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'request_data' => $request->all(),
                    'headers' => $request->headers->all(),
                ]);
            }
            
            // Log the error
            Log::error('Unhandled exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            if ($request->expectsJson()) {
                return \App\Helpers\JsonResponseHelper::error(
                    app()->environment('production')
                        ? 'An error occurred. Please try again later.'
                        : $e->getMessage(),
                    'INTERNAL_SERVER_ERROR',
                    null,
                    500
                );
            }

            // Always show custom error page (even in development)
            // Error details are shown in the custom page for development environments
            if ($request->is('admin/*')) {
                try {
                    return response()->view('admin.errors.500', ['exception' => $e], 500);
                } catch (\Throwable $viewError) {
                    // Fallback if view fails - prevent infinite loop by using simple HTML
                    Log::error('Failed to render 500 error page: ' . $viewError->getMessage());
                    return response('<h1>500 - Internal Server Error</h1><p>An error occurred. Please <a href="' . route('admin.dashboard') . '">go back</a>.</p>', 500)
                        ->header('Content-Type', 'text/html');
                }
            }

            try {
                return response()->view('frontend.errors.500', ['exception' => $e], 500);
            } catch (\Throwable $viewError) {
                // Fallback if view fails - prevent infinite loop by using simple HTML without layout
                Log::error('Failed to render 500 error page: ' . $viewError->getMessage());
                return response('<!DOCTYPE html><html><head><title>500 - Internal Server Error</title></head><body><h1>500 - Internal Server Error</h1><p>An error occurred. Please try again later.</p></body></html>', 500)
                    ->header('Content-Type', 'text/html');
            }
        });
    })->create();
