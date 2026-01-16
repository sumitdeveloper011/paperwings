<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Services\ApiKeyEncryptionService;
use Illuminate\Support\Facades\Cache;

class SettingHelper
{
    // Get all settings (cached, but sensitive keys retrieved directly)
    public static function all(): array
    {
        try {
            // Get non-sensitive settings from cache
            $cachedSettings = Cache::remember('app_settings', 3600, function() {
                try {
                    $allSettings = Setting::pluck('value', 'key')->toArray();
                    // Remove sensitive keys from cache
                    $sensitiveKeys = ApiKeyEncryptionService::getEncryptedKeys();
                    foreach ($sensitiveKeys as $key) {
                        unset($allSettings[$key]);
                    }
                    return $allSettings;
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to fetch settings from database: ' . $e->getMessage());
                    return [];
                }
            });

            // Get sensitive keys directly from database (not cached)
            $sensitiveKeys = ApiKeyEncryptionService::getEncryptedKeys();
            foreach ($sensitiveKeys as $key) {
                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $cachedSettings[$key] = ApiKeyEncryptionService::decrypt($key, $setting->value);
                }
            }

            return $cachedSettings;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to access settings: ' . $e->getMessage());
            return [];
        }
    }

    // Get a specific setting value (with decryption and .env fallback)
    public static function get(string $key, $default = null)
    {
        // Get from database
        $settings = self::all();
        $value = $settings[$key] ?? null;

        // If not in database, try .env file (for frontend fallback)
        if (empty($value)) {
            $envKey = self::getEnvKeyName($key);
            if ($envKey) {
                $value = env($envKey);
            }
        }

        return $value ?? $default;
    }

    /**
     * Map setting key to .env variable name
     *
     * @param string $key Setting key
     * @return string|null .env variable name or null
     */
    private static function getEnvKeyName(string $key): ?string
    {
        $mapping = [
            'stripe_key' => 'STRIPE_KEY',
            'stripe_secret' => 'STRIPE_SECRET',
            'stripe_webhook_secret' => 'STRIPE_WEBHOOK_SECRET',
            'google_client_id' => 'GOOGLE_CLIENT_ID',
            'google_client_secret' => 'GOOGLE_CLIENT_SECRET',
            'facebook_client_id' => 'FACEBOOK_CLIENT_ID',
            'facebook_client_secret' => 'FACEBOOK_CLIENT_SECRET',
            'eposnow_api_key' => 'EPOSNOW_API_KEY',
            'eposnow_api_secret' => 'EPOSNOW_API_SECRET',
            'eposnow_api_base' => 'EPOSNOW_API_BASE',
            'nzpost_api_key' => 'NZPOST_API_KEY',
            'google_map_api_key' => 'GOOGLE_MAP_API_KEY',
            'instagram_app_id' => 'INSTAGRAM_APP_ID',
            'instagram_app_secret' => 'INSTAGRAM_APP_SECRET',
            'instagram_access_token' => 'INSTAGRAM_ACCESS_TOKEN',
            'instagram_user_id' => 'INSTAGRAM_USER_ID',
        ];

        return $mapping[$key] ?? null;
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
