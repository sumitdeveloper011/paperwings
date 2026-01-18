<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tax Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains tax-related configuration values.
    | For New Zealand, GST (Goods and Services Tax) is 15%.
    |
    */

    'gst_rate' => env('GST_RATE', 15), // GST rate as percentage (e.g., 15 for 15%)
    
    'gst_decimal' => env('GST_RATE', 15) / 100, // GST rate as decimal (e.g., 0.15 for 15%)
    
    'gst_multiplier' => 1 + (env('GST_RATE', 15) / 100), // Multiplier for price with tax (e.g., 1.15 for 15% GST)
];
