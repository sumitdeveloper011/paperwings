<?php

namespace App\Helpers;

class CriticalCssHelper
{
    /**
     * Get critical CSS content
     *
     * @return string
     */
    public static function get(): string
    {
        $criticalCssPath = public_path('assets/frontend/css/critical.css');
        
        if (file_exists($criticalCssPath)) {
            return file_get_contents($criticalCssPath);
        }
        
        return '';
    }
    
    /**
     * Check if critical CSS file exists
     *
     * @return bool
     */
    public static function exists(): bool
    {
        return file_exists(public_path('assets/frontend/css/critical.css'));
    }
}
