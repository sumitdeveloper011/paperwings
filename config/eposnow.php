<?php

return [
    'api_key' => env('EPOSNOW_API_KEY'),
    'api_secret' => env('EPOSNOW_API_SECRET'),
    'api_base' => env('EPOSNOW_API_BASE'),    
    'default_category_id' => env('EPOSNOW_DEFAULT_CATEGORY_ID', null),    
    'rate_limit' => [
        'max_calls_per_minute' => env('EPOSNOW_MAX_CALLS_PER_MINUTE', 100),        
        'safe_threshold' => env('EPOSNOW_SAFE_THRESHOLD', 80),        
        'cooldown_minutes' => env('EPOSNOW_COOLDOWN_MINUTES', 10),        
        'min_delay' => env('EPOSNOW_MIN_DELAY', 0.6),        
        'max_delay' => env('EPOSNOW_MAX_DELAY', 3.0),
    ],
];
