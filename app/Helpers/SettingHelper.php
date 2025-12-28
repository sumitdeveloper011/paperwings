<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingHelper
{
    // Get all settings (cached)
    public static function all(): array
    {
        return Cache::remember('app_settings', 3600, function() {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    // Get a specific setting value (cached)
    public static function get(string $key, $default = null)
    {
        $settings = self::all();
        return $settings[$key] ?? $default;
    }

    // Get logo URL with fallback
    public static function logo(): string
    {
        $logo = self::get('logo');
        if ($logo && !empty($logo)) {
            return asset('storage/' . $logo);
        }
        return asset('assets/images/logo.svg');
    }

    // Get favicon URL with fallback
    public static function favicon(): string
    {
        $favicon = self::get('icon');
        if ($favicon && !empty($favicon)) {
            return asset('storage/' . $favicon);
        }
        return asset('assets/images/favicon.ico');
    }

    // Get site name with fallback
    public static function siteName(): string
    {
        return self::get('site_name', 'PAPERWINGS');
    }

    // Clear settings cache
    public static function clearCache(): void
    {
        Cache::forget('app_settings');
    }
}

