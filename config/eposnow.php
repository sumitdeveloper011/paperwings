<?php

return [
    'api_key' => env('EPOSNOW_API_KEY'),
    'api_secret' => env('EPOSNOW_API_SECRET'),
    'api_base' => env('EPOSNOW_API_BASE'),
    
    // Default category ID to use when importing products from EposNow
    // if the product's category is not found in the local database
    // Set to null to skip products without a valid category
    'default_category_id' => env('EPOSNOW_DEFAULT_CATEGORY_ID', null),
];
