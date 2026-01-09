<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Setting;
use App\Models\User;
use App\Helpers\CommonHelper;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Display login page
    public function login(Request $request)
    {
        try {
            if (Auth::check() && CommonHelper::hasAnyRole(Auth::user(), ['Admin', 'SuperAdmin'])) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Please logout from admin account to access user login.');
            }

            if (Auth::check()) {
                return redirect()->route('home');
            }

            if ($request->has('intended')) {
                session(['url.intended' => $request->intended]);
            }

            $googleLoginEnabled = Setting::get('google_login_enabled', '0') == '1';
            $facebookLoginEnabled = Setting::get('facebook_login_enabled', '0') == '1';

            return view('include.frontend.login', compact('googleLoginEnabled', 'facebookLoginEnabled'));
        } catch (\Exception $e) {
            Log::error('Login page error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Fallback values if settings fail
            return view('include.frontend.login', [
                'googleLoginEnabled' => false,
                'facebookLoginEnabled' => false
            ]);
        }
    }

    // Handle user login with full security
    public function authenticate(LoginRequest $request)
    {
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

        $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);
        $password = $request->password;

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

        $credentials = ['email' => $email, 'password' => $password];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (CommonHelper::hasAnyRole($user, ['Admin', 'SuperAdmin'])) {
                Auth::logout();

                CommonHelper::logSecurityEvent('Admin attempted frontend login', $user, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return back()->withErrors([
                    'email' => 'Admin users must login through the admin panel. Please use /admin/login'
                ])->onlyInput('email');
            }

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

            // Check if email is verified
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();

                CommonHelper::logSecurityEvent('Unverified email login attempt', $user, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return back()->withErrors([
                    'email' => 'Please verify your email address before logging in. Check your inbox for the verification link.'
                ])->onlyInput('email');
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($key);

            // Regenerate session for security
            $request->session()->regenerate();

            // Log successful login
            CommonHelper::logSecurityEvent('Successful user login', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clear any intended URL from session to prevent redirecting to admin routes
            $request->session()->forget('url.intended');

            // Always redirect frontend users to home page
            return redirect()->route('home')->with('success', 'Welcome back! You have been successfully logged in.');
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

    public function register(Request $request)
    {
        try {
            // If already authenticated, check role and redirect accordingly
            if (Auth::check()) {
                try {
                    if (CommonHelper::hasAnyRole(Auth::user(), ['Admin', 'SuperAdmin'])) {
                        return redirect()->route('admin.dashboard')
                            ->with('error', 'Please logout from admin account to register a new user account.');
                    }
                } catch (\Exception $e) {
                    Log::warning('Role check failed in register method: ' . $e->getMessage());
                }

                // If already authenticated as user, redirect to home
                return redirect()->route('home');
            }

            // Get social login settings with error handling
            try {
                $googleLoginEnabled = Setting::get('google_login_enabled', '0') == '1';
                $facebookLoginEnabled = Setting::get('facebook_login_enabled', '0') == '1';
            } catch (\Exception $e) {
                Log::warning('Settings retrieval failed in register method: ' . $e->getMessage());
                $googleLoginEnabled = false;
                $facebookLoginEnabled = false;
            }

            return view('include.frontend.register', compact('googleLoginEnabled', 'facebookLoginEnabled'));
        } catch (\Exception $e) {
            Log::error('Register page error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // Fallback values if anything fails
            return view('include.frontend.register', [
                'googleLoginEnabled' => false,
                'facebookLoginEnabled' => false
            ]);
        }
    }

    public function store(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'agree_terms' => $validated['agreeTerms'] ? 1 : 0,
                'status' => 0,
            ]);

            try {
                if (\Spatie\Permission\Models\Role::where('name', 'User')->exists()) {
                    $user->assignRole('User');
                }
            } catch (\Exception $e) {
                // Role might not exist, continue without role assignment
                Log::warning('Could not assign User role: ' . $e->getMessage());
            }

            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                // Log email notification error but don't fail registration
                Log::error('Email verification notification failed: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            }

            return redirect()->route('register')->with('success', 'Registration successful! Please check your email to verify your account.');

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating your account. Please try again.');
        }
    }

    public function forgotPassword()
    {
        return view('include.frontend.forgot-password');
    }

    // Send password reset link
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        try {
            $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);

            // Security checks - SQL Injection and XSS detection
            if (CommonHelper::detectSqlInjection($email) || CommonHelper::detectXss($email)) {
                CommonHelper::logSecurityEvent('Malicious input detected in password reset request', null, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'input_type' => 'email'
                ]);

                return back()->withErrors([
                    'email' => 'Invalid input detected. Please check your email address.'
                ])->onlyInput('email');
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                // Don't reveal if email exists for security
                return back()->with('success', 'If that email address exists in our system, we have sent a password reset link.');
            }

            // Generate password reset token
            $token = Str::random(64);

            // Store token in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Send password reset notification
            $user->notify(new ResetPasswordNotification($token));

            // Log security event
            CommonHelper::logSecurityEvent('Password reset link requested', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('success', 'If that email address exists in our system, we have sent a password reset link.');

        } catch (\Exception $e) {
            Log::error('Password reset request error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }

    // Show password reset form
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('forgot-password')->with('error', 'Invalid reset link.');
        }

        // Verify token exists and is valid
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$resetRecord) {
            return redirect()->route('forgot-password')->with('error', 'Invalid or expired reset link.');
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('forgot-password')->with('error', 'This password reset link has expired. Please request a new one.');
        }

        // Verify token matches
        if (!Hash::check($token, $resetRecord->token)) {
            return redirect()->route('forgot-password')->with('error', 'Invalid reset link.');
        }

        return view('include.frontend.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    // Handle password reset
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);
            $token = $request->token;
            $password = $request->password;

            // Security checks
            if (CommonHelper::detectSqlInjection($email) || CommonHelper::detectXss($email)) {
                CommonHelper::logSecurityEvent('Malicious input detected in password reset', null, [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'input_type' => 'email'
                ]);

                return back()->withErrors([
                    'email' => 'Invalid input detected.'
                ])->withInput();
            }

            // Verify token
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();

            if (!$resetRecord) {
                return back()->withErrors([
                    'email' => 'Invalid or expired reset link.'
                ])->withInput();
            }

            // Check if token is expired (60 minutes)
            if (now()->diffInMinutes($resetRecord->created_at) > 60) {
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                return back()->withErrors([
                    'email' => 'This password reset link has expired. Please request a new one.'
                ])->withInput();
            }

            // Verify token matches
            if (!Hash::check($token, $resetRecord->token)) {
                return back()->withErrors([
                    'email' => 'Invalid reset link.'
                ])->withInput();
            }

            // Find user
            $user = User::where('email', $email)->first();

            if (!$user) {
                return back()->withErrors([
                    'email' => 'User not found.'
                ])->withInput();
            }

            // Update password
            $user->password = Hash::make($password);
            $user->save();

            // Delete used token
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            // Log security event
            CommonHelper::logSecurityEvent('Password reset successful', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please login with your new password.');

        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->with('error', 'An error occurred while resetting your password. Please try again.')->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    // Handle email verification
    public function verify(Request $request, $id, $hash)
    {
        // Verify the signed URL
        if (!URL::hasValidSignature($request)) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
        }

        $user = User::findOrFail($id);

        // Verify the hash matches
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('info', 'Your email is already verified. You can now log in.');
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            // Update user status to active (1) after email verification
            $user->status = 1;
            $user->save();

            return redirect()->route('login')->with('success', 'Your email has been verified successfully! You can now log in.');
        }

        return redirect()->route('login')->with('error', 'Email verification failed. Please try again.');
    }
}
