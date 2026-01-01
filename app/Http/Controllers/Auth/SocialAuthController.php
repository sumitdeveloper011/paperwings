<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // Redirect to Google OAuth
    public function redirectToGoogle(): RedirectResponse
    {
        $googleEnabled = Setting::get('google_login_enabled', '0');
        if ($googleEnabled != '1') {
            return redirect()->route('login')
                ->with('error', 'Google login is currently disabled.');
        }

        // Configure Socialite with database settings
        $googleClientId = Setting::get('google_client_id');
        $googleClientSecret = Setting::get('google_client_secret');
        
        if (empty($googleClientId) || empty($googleClientSecret)) {
            return redirect()->route('login')
                ->with('error', 'Google OAuth credentials are not configured.');
        }

        config([
            'services.google.client_id' => $googleClientId,
            'services.google.client_secret' => $googleClientSecret,
            'services.google.redirect' => route('auth.google.callback'),
        ]);

        return Socialite::driver('google')->redirect();
    }

    // Handle Google OAuth callback
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            // Configure Socialite with database settings
            $googleClientId = Setting::get('google_client_id');
            $googleClientSecret = Setting::get('google_client_secret');
            
            if (empty($googleClientId) || empty($googleClientSecret)) {
                return redirect()->route('login')
                    ->with('error', 'Google OAuth credentials are not configured.');
            }

            config([
                'services.google.client_id' => $googleClientId,
                'services.google.client_secret' => $googleClientSecret,
                'services.google.redirect' => route('auth.google.callback'),
            ]);

            $socialUser = Socialite::driver('google')->user();

            $user = User::where('email', $socialUser->getEmail())
                ->orWhere(function($query) use ($socialUser) {
                    $query->where('provider', 'google')
                          ->where('provider_id', $socialUser->getId());
                })
                ->first();

            if ($user) {
                // Update existing user with social provider info if not set
                if (!$user->provider) {
                    $user->update([
                        'provider' => 'google',
                        'provider_id' => $socialUser->getId(),
                    ]);
                }
            } else {
                // Create new user
                $nameParts = $this->splitName($socialUser->getName());
                
                $user = User::create([
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name'],
                    'email' => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(32)),
                    'provider' => 'google',
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'status' => 1,
                    'agree_terms' => 1,
                ]);
            }

            Auth::login($user, true);

            // Clear any intended URL from session to prevent redirecting to admin routes
            request()->session()->forget('url.intended');
            
            // Always redirect frontend users to home page
            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Unable to login with Google. Please try again.');
        }
    }

    // Redirect to Facebook OAuth
    public function redirectToFacebook(): RedirectResponse
    {
        $facebookEnabled = Setting::get('facebook_login_enabled', '0');
        if ($facebookEnabled != '1') {
            return redirect()->route('login')
                ->with('error', 'Facebook login is currently disabled.');
        }

        // Configure Socialite with database settings
        $facebookClientId = Setting::get('facebook_client_id');
        $facebookClientSecret = Setting::get('facebook_client_secret');
        
        if (empty($facebookClientId) || empty($facebookClientSecret)) {
            return redirect()->route('login')
                ->with('error', 'Facebook OAuth credentials are not configured.');
        }

        config([
            'services.facebook.client_id' => $facebookClientId,
            'services.facebook.client_secret' => $facebookClientSecret,
            'services.facebook.redirect' => route('auth.facebook.callback'),
        ]);

        return Socialite::driver('facebook')->redirect();
    }

    // Handle Facebook OAuth callback
    public function handleFacebookCallback(): RedirectResponse
    {
        try {
            // Configure Socialite with database settings
            $facebookClientId = Setting::get('facebook_client_id');
            $facebookClientSecret = Setting::get('facebook_client_secret');
            
            if (empty($facebookClientId) || empty($facebookClientSecret)) {
                return redirect()->route('login')
                    ->with('error', 'Facebook OAuth credentials are not configured.');
            }

            config([
                'services.facebook.client_id' => $facebookClientId,
                'services.facebook.client_secret' => $facebookClientSecret,
                'services.facebook.redirect' => route('auth.facebook.callback'),
            ]);

            $socialUser = Socialite::driver('facebook')->user();

            $user = User::where('email', $socialUser->getEmail())
                ->orWhere(function($query) use ($socialUser) {
                    $query->where('provider', 'facebook')
                          ->where('provider_id', $socialUser->getId());
                })
                ->first();

            if ($user) {
                // Update existing user with social provider info if not set
                if (!$user->provider) {
                    $user->update([
                        'provider' => 'facebook',
                        'provider_id' => $socialUser->getId(),
                    ]);
                }
            } else {
                // Create new user
                $nameParts = $this->splitName($socialUser->getName());
                
                $user = User::create([
                    'first_name' => $nameParts['first_name'],
                    'last_name' => $nameParts['last_name'],
                    'email' => $socialUser->getEmail() ?? $socialUser->getId() . '@facebook.com',
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(32)),
                    'provider' => 'facebook',
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'status' => 1,
                    'agree_terms' => 1,
                ]);
            }

            Auth::login($user, true);

            // Clear any intended URL from session to prevent redirecting to admin routes
            request()->session()->forget('url.intended');
            
            // Always redirect frontend users to home page
            return redirect()->route('home');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Unable to login with Facebook. Please try again.');
        }
    }

    // Split full name into first and last name
    private function splitName(string $fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);
        
        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }
}
