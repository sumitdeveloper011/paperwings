<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Category Slugs
    |--------------------------------------------------------------------------
    |
    | These are the slugs for default categories used throughout the application.
    | You can change these slugs if needed, but make sure to update the
    | categories in the database accordingly.
    |
    */

    'special_combos_slug' => env('CATEGORY_SPECIAL_COMBOS_SLUG', 'special-combos'),
    
    'general_products_slug' => env('CATEGORY_GENERAL_PRODUCTS_SLUG', 'general-products'),
];
