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
    | Bundle Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for bundle display (same as products):
    | - Original: 1200x1200px for bundle detail pages
    | - Medium: 600x600px for bundle galleries
    | - Thumbnail: 300x300px for bundle listings and grids
    */
    'bundle' => [
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
    | Page Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for page banner display:
    | - Original (Main): 1200x400px for page banner display (3:1 ratio)
    | - Medium: 600x200px for medium-sized displays
    | - Thumbnail: 300x100px for admin listings and thumbnails
    */
    'page' => [
        'original' => [
            'width' => 1200,
            'height' => 400,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'medium' => [
            'width' => 600,
            'height' => 200,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 300,
            'height' => 100,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | About Section Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for about section display:
    | - Original (Main): 600x400px for about section display (3:2 ratio)
    | - Thumbnail: 300x200px for admin listings and thumbnails
    */
    'about-section' => [
        'original' => [
            'width' => 600,
            'height' => 400,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 300,
            'height' => 200,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for gallery display:
    | - Original (Main): 2000x2000px for gallery images
    | - Medium: 1000x1000px for medium-sized displays
    | - Thumbnail: 300x300px for admin listings and thumbnails
    */
    'gallery' => [
        'original' => [
            'width' => 2000,
            'height' => 2000,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'medium' => [
            'width' => 1000,
            'height' => 1000,
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
    | Testimonial Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for testimonial profile images:
    | - Original (Main): 200x200px for testimonial profile display (square)
    | - Thumbnail: 100x100px for admin listings and thumbnails
    */
    'testimonial' => [
        'original' => [
            'width' => 200,
            'height' => 200,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 100,
            'height' => 100,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Special Offers Banner Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for special offers banner display:
    | - Original (Main): 1920x450px for banner display (4.27:1 ratio)
    | - Medium: 960x225px for medium-sized displays
    | - Thumbnail: 480x112px for admin listings and thumbnails
    */
    'special-offers' => [
        'original' => [
            'width' => 1920,
            'height' => 450,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'medium' => [
            'width' => 960,
            'height' => 225,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 480,
            'height' => 112,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for user profile images:
    | - Original (Main): 200x200px for profile display (square)
    | - Thumbnail: 100x100px for admin listings and thumbnails
    */
    'user' => [
        'original' => [
            'width' => 200,
            'height' => 200,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 100,
            'height' => 100,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for site logo display:
    | - Original (Main): 1640x762px for main logo display
    | - Medium: 410x190px for medium-sized displays
    | - Thumbnail: 300x140px for admin listings and thumbnails
    */
    'logo' => [
        'original' => [
            'width' => 1640,
            'height' => 762,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'medium' => [
            'width' => 410,
            'height' => 190,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 300,
            'height' => 140,
            'quality' => 85,
            'preserve_aspect_ratio' => false, // Force exact dimensions
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Icon (Favicon) Image Settings
    |--------------------------------------------------------------------------
    | Dimensions optimized for favicon/icon display:
    | - Original (Main): 64x64px for favicon display (square)
    | - Thumbnail: 32x32px for standard favicon size (square)
    */
    'icon' => [
        'original' => [
            'width' => 64,
            'height' => 64,
            'quality' => 90,
            'preserve_aspect_ratio' => false, // Force square crop for consistency
            'fit' => 'cover', // Cover ensures image fills dimensions
        ],
        'thumbnail' => [
            'width' => 32,
            'height' => 32,
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
