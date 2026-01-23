<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingHelper
{
    // Get all settings (cached)
    public static function all(): array
    {
        try {
            // Check if database connection is available
            if (!self::isDatabaseAvailable()) {
                return [];
            }

            // Get all settings from cache
            return Cache::remember(CacheHelper::APP_SETTINGS, 3600, function() {
                try {
                    if (!self::isDatabaseAvailable()) {
                        return [];
                    }
                    return Setting::pluck('value', 'key')->toArray();
                } catch (\Illuminate\Database\QueryException $e) {
                    // Database connection errors - don't log as warning, just return empty
                    if (str_contains($e->getMessage(), 'Unknown database') || 
                        str_contains($e->getMessage(), 'Connection refused') ||
                        str_contains($e->getMessage(), 'Access denied')) {
                        return [];
                    }
                    \Illuminate\Support\Facades\Log::warning('Failed to fetch settings from database: ' . $e->getMessage());
                    return [];
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to fetch settings from database: ' . $e->getMessage());
                    return [];
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Database connection errors - return empty array silently
            if (str_contains($e->getMessage(), 'Unknown database') || 
                str_contains($e->getMessage(), 'Connection refused') ||
                str_contains($e->getMessage(), 'Access denied')) {
                return [];
            }
            \Illuminate\Support\Facades\Log::warning('Failed to access settings: ' . $e->getMessage());
            return [];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to access settings: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if database connection is available
     *
     * @return bool
     */
    private static function isDatabaseAvailable(): bool
    {
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get a specific setting value from database
     *
     * @param string $key Setting key
     * @param mixed $default Default value if setting not found
     * @return mixed Setting value or default
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::all();
        return $settings[$key] ?? $default;
    }

    // Get logo URL with fallback (returns medium size for frontend)
    public static function logo(): string
    {
        $logo = self::get('logo');
        if ($logo && !empty($logo)) {
            // Check if logo path contains '/original/' (new structure)
            if (strpos($logo, '/original/') !== false) {
                // Convert original path to medium path
                $mediumPath = str_replace('/original/', '/medium/', $logo);
                
                // Check if medium version exists, otherwise fallback to original
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($mediumPath)) {
                    return asset('storage/' . $mediumPath);
                }
            }
            
            // Fallback to original if medium doesn't exist or old structure
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
        return asset('assets/frontend/images/icon.png');
    }

    // Get site name with fallback
    public static function siteName(): string
    {
        return self::get('site_name', 'PAPERWINGS');
    }

    // Clear settings cache
    public static function clearCache(): void
    {
        Cache::forget(CacheHelper::APP_SETTINGS);
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
