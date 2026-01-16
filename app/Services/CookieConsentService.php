<?php

namespace App\Services;

use App\Models\CookiePreference;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CookieConsentService
{
    protected string $cookieName = 'cookie_consent_preferences';
    protected int $cookieExpiry = 525600; // 1 year in minutes

    public function hasConsent(string $category): bool
    {
        $preferences = $this->getPreferences();

        return match($category) {
            'essential' => true,
            'analytics' => $preferences['analytics_cookies'] ?? false,
            'marketing' => $preferences['marketing_cookies'] ?? false,
            'functionality' => $preferences['functionality_cookies'] ?? false,
            default => false,
        };
    }

    public function getPreferences(): array
    {
        $cookie = Cookie::get($this->cookieName);
        
        if (!$cookie) {
            return [
                'essential_cookies' => true,
                'analytics_cookies' => false,
                'marketing_cookies' => false,
                'functionality_cookies' => false,
            ];
        }

        $decoded = json_decode($cookie, true);
        
        return [
            'essential_cookies' => $decoded['essential_cookies'] ?? true,
            'analytics_cookies' => $decoded['analytics_cookies'] ?? false,
            'marketing_cookies' => $decoded['marketing_cookies'] ?? false,
            'functionality_cookies' => $decoded['functionality_cookies'] ?? false,
        ];
    }

    public function savePreferences(array $preferences, ?int $userId = null): void
    {
        $preferences['essential_cookies'] = true;
        $preferences['preferences_saved_at'] = now()->toIso8601String();

        $cookie = Cookie::make(
            $this->cookieName,
            json_encode($preferences),
            $this->cookieExpiry,
            '/',
            null,
            true,
            true,
            false,
            'Lax'
        );

        Cookie::queue($cookie);

        if (config('app.save_cookie_preferences_to_db', false)) {
            $this->saveToDatabase($preferences, $userId);
        }
    }

    protected function saveToDatabase(array $preferences, ?int $userId = null): void
    {
        CookiePreference::create([
            'uuid' => Str::uuid(),
            'session_id' => session()->getId(),
            'user_id' => $userId ?? auth()->id(),
            'essential_cookies' => $preferences['essential_cookies'] ?? true,
            'analytics_cookies' => $preferences['analytics_cookies'] ?? false,
            'marketing_cookies' => $preferences['marketing_cookies'] ?? false,
            'functionality_cookies' => $preferences['functionality_cookies'] ?? false,
            'preferences_saved_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function hasConsentCookie(): bool
    {
        return Cookie::has($this->cookieName);
    }
}
