<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Thumbnail Settings (Default)
    |--------------------------------------------------------------------------
    */
    'thumbnail' => [
        'width' => 400,
        'height' => 400,
        'quality' => 85,
        'preserve_aspect_ratio' => true,
        'fit' => 'contain', // contain, cover, fill, inside, outside
    ],

    /*
    |--------------------------------------------------------------------------
    | Category Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for frontend display:
    | - Frontend displays category images in 1:1 aspect ratio (square)
    | - Original: 500x500px for optimal display
    | - Thumbnail: 200x200px for grid listings and admin tables
    */
    'category' => [
        'original' => [
            'width' => 500,
            'height' => 500,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills the square completely
        ],
        'thumbnail' => [
            'width' => 200,
            'height' => 200,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills the square completely
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Subcategory Image Settings
    |--------------------------------------------------------------------------
    | Same as category - square format for consistency
    */
    'subcategory' => [
        'original' => [
            'width' => 800,
            'height' => 800,
            'quality' => 90,
            'preserve_aspect_ratio' => false,
            'fit' => 'cover',
        ],
        'thumbnail' => [
            'width' => 400,
            'height' => 400,
            'quality' => 85,
            'preserve_aspect_ratio' => false,
            'fit' => 'cover',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Slider Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for banner/slider display:
    | - Original: 1920x600px for full-width banner display
    | - Thumbnail: 320x100px for admin listings
    */
    'slider' => [
        'original' => [
            'width' => 1920,
            'height' => 600,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 320,
            'height' => 100,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force exact dimensions to match 3.2:1 ratio
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Product Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for product display:
    | - Original: 1200x1200px for product detail pages
    | - Medium: 600x600px for product galleries
    | - Thumbnail: 300x300px for product listings and grids
    */
    'product' => [
        'original' => [
            'width' => 1200,
            'height' => 1200,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'medium' => [
            'width' => 600,
            'height' => 600,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 300,
            'height' => 300,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    */
    'optimization' => [
        'enabled' => true,
        'strip_exif' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Settings
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'disk' => 'public',
        'generate_on_upload' => true,
    ],
];
