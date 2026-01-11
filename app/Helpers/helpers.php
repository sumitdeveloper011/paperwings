<?php

use App\Helpers\SettingHelper;

if (!function_exists('setting')) {
    // Get a setting value (cached)
    function setting(?string $key = null, $default = null)
    {
        if ($key === null) {
            return SettingHelper::all();
        }
        return SettingHelper::get($key, $default);
    }
}

if (!function_exists('site_logo')) {
    // Get site logo URL with fallback
    function site_logo(): string
    {
        return SettingHelper::logo();
    }
}

if (!function_exists('site_favicon')) {
    // Get site favicon URL with fallback
    function site_favicon(): string
    {
        return SettingHelper::favicon();
    }
}

if (!function_exists('site_name')) {
    // Get site name with fallback
    function site_name(): string
    {
        return SettingHelper::siteName();
    }
}

if (!function_exists('role_display_name')) {
    // Get role display name with better formatting
    function role_display_name(string $roleName): string
    {
        $roleMap = [
            'SuperAdmin' => 'Chief Administrator',
            'Admin' => 'Site Administrator',
            'Manager' => 'Store Manager',
            'Editor' => 'Editorial Staff',
            'User' => 'Customer',
        ];

        return $roleMap[$roleName] ?? ucwords(str_replace('_', ' ', $roleName));
    }
}

