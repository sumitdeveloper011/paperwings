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
        return asset('assets/frontend/images/logo.png');
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

    /**
     * Extract social links from settings array
     *
     * @param array $settings Settings array
     * @return array Social links array
     */
    public static function extractSocialLinks(array $settings): array
    {
        $platforms = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest'];
        $socialLinks = [];

        foreach ($platforms as $platform) {
            $key = "social_{$platform}";
            if (!empty($settings[$key])) {
                $socialLinks[$platform] = $settings[$key];
            }
        }

        return $socialLinks;
    }

    /**
     * Get first value from array setting (phones/emails)
     *
     * @param array $settings Settings array
     * @param string $key Setting key (e.g., 'phones', 'emails')
     * @return string|null First value or null
     */
    public static function getFirstFromArraySetting(array $settings, string $key): ?string
    {
        $value = $settings[$key] ?? null;

        if (is_string($value)) {
            $decoded = json_decode($value, true) ?? [];
            return !empty($decoded) ? $decoded[0] : null;
        }

        if (is_array($value)) {
            return !empty($value) ? $value[0] : null;
        }

        return null;
    }

    /**
     * Get array from array setting (phones/emails)
     *
     * @param array $settings Settings array
     * @param string $key Setting key (e.g., 'phones', 'emails')
     * @return array Array of values
     */
    public static function getArraySetting(array $settings, string $key): array
    {
        $value = $settings[$key] ?? null;

        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        if (is_array($value)) {
            return $value;
        }

        return [];
    }
}
