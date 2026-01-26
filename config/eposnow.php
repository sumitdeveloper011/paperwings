<?php

return [
    'api_key' => env('EPOSNOW_API_KEY'),
    'api_secret' => env('EPOSNOW_API_SECRET'),
    'api_base' => env('EPOSNOW_API_BASE'),
    
    // Default category ID to use when importing products from EposNow
    // if the product's category is not found in the local database
    // Set to null to skip products without a valid category
    'default_category_id' => env('EPOSNOW_DEFAULT_CATEGORY_ID', null),
    
    // Rate limit configuration
    'rate_limit' => [
        // Maximum API calls allowed per minute
        'max_calls_per_minute' => env('EPOSNOW_MAX_CALLS_PER_MINUTE', 100),
        
        // Safe threshold - pause when reaching this percentage
        'safe_threshold' => env('EPOSNOW_SAFE_THRESHOLD', 80),
        
        // Cooldown period in minutes after hitting rate limit
        'cooldown_minutes' => env('EPOSNOW_COOLDOWN_MINUTES', 30),
        
        // Minimum delay between API calls (seconds)
        'min_delay' => env('EPOSNOW_MIN_DELAY', 0.5),
        
        // Maximum delay between API calls (seconds)
        'max_delay' => env('EPOSNOW_MAX_DELAY', 3.0),
    ],
];
