<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CommonHelper;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function login()
    {
        // If already authenticated, redirect to dashboard
        if (Auth::check() && CommonHelper::hasAnyRole(Auth::user(), ['Admin', 'SuperAdmin'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle authentication
     */
    public function authenticate(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ], [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        // Rate limiting check
        $key = 'login-attempts:' . $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 15;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            CommonHelper::logSecurityEvent('Rate limit exceeded for login', null, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'attempts' => RateLimiter::attempts($key)
            ]);

            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'
            ])->onlyInput('email');
        }

        // Sanitize input
        $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);
        $password = $request->password;

        // Security checks
        if (CommonHelper::detectSqlInjection($email) || CommonHelper::detectXss($email)) {
            RateLimiter::hit($key, $decayMinutes * 60);
            
            CommonHelper::logSecurityEvent('Malicious input detected in login attempt', null, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'input_type' => 'email'
            ]);

            return back()->withErrors([
                'email' => 'Invalid input detected. Please check your credentials.'
            ])->onlyInput('email');
        }

        // Attempt authentication
        $credentials = ['email' => $email, 'password' => $password];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is active
            if (!$user->isActive()) {
                Auth::logout();
                
                CommonHelper::logSecurityEvent('Inactive user login attempt', $user, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact administrator.'
                ])->onlyInput('email');
            }

            // Check if user has admin role
            if (!CommonHelper::hasAnyRole($user, ['Admin', 'SuperAdmin'])) {
                Auth::logout();
                
                CommonHelper::logSecurityEvent('Non-admin user login attempt', $user, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_roles' => $user->roles->pluck('name')->toArray()
                ]);

                return back()->withErrors([
                    'email' => 'You do not have permission to access the admin panel.'
                ])->onlyInput('email');
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($key);

            // Regenerate session for security
            $request->session()->regenerate();

            // Log successful login
            CommonHelper::logSecurityEvent('Successful admin login', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_roles' => $user->roles->pluck('name')->toArray()
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        // Failed login attempt
        RateLimiter::hit($key, $decayMinutes * 60);
        
        CommonHelper::logSecurityEvent('Failed login attempt', null, [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'email' => CommonHelper::maskData($email, 'email')
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            CommonHelper::logSecurityEvent('Admin logout', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been successfully logged out.');
    }
}
