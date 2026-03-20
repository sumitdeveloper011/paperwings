<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Only add Strict-Transport-Security in production with HTTPS
        if (app()->environment('production') && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Always set a full CSP from the app so policy is not dependent on APP_ENV.
        // If hosting injects a second CSP, browsers enforce both and host-level CSP must be aligned too.
        $csp = implode(' ', [
            "default-src 'self' data:;",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com https://cdn.jsdelivr.net https://*.wsimg.com;",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;",
            "img-src 'self' data: blob: https: https://*.cdninstagram.com https://*.fbcdn.net;",
            "font-src 'self' data: https://cdn.jsdelivr.net;",
            "connect-src 'self' https://api.stripe.com https://*.secureserver.net;",
            "frame-src https://js.stripe.com;",
        ]);

        $response->headers->remove('Content-Security-Policy');
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
