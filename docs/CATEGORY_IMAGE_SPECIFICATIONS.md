# Category Image Specifications

## Overview

This document outlines the image specifications and requirements for category images in the Paper Wings application. Category images are automatically processed to ensure optimal display on both the admin panel and frontend.

## Image Dimensions

### Original Image
- **Width:** 500 pixels
- **Height:** 500 pixels
- **Aspect Ratio:** 1:1 (Square)
- **Quality:** 90%
- **Fit Method:** Cover (image is cropped to fit square dimensions)

### Thumbnail Image
- **Width:** 200 pixels
- **Height:** 200 pixels
- **Aspect Ratio:** 1:1 (Square)
- **Quality:** 85%
- **Fit Method:** Cover (image is cropped to fit square dimensions)

## Why Square Images?

Category images are displayed in a square format on the frontend:
- Frontend category cards use a 1:1 aspect ratio container
- Square images ensure consistent, professional appearance across all categories
- Prevents image distortion and maintains visual consistency

## File Requirements

### Accepted Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)

### File Size
- **Maximum:** 2MB (2048 KB)
- **Recommended:** Under 1MB for faster loading

### Image Quality Guidelines
- Use high-quality source images (minimum 500x500px)
- Images will be automatically resized and optimized during upload
- Avoid images with excessive compression artifacts
- Ensure images are clear and well-lit

## Upload Process

### Automatic Processing

When a category image is uploaded:

1. **Original Image Processing:**
   - Image is resized to exactly 500x500 pixels
   - Aspect ratio is maintained, but image is cropped to fit square dimensions
   - Image is saved with 90% quality
   - Stored in: `storage/app/public/categories/{uuid}/original/`

2. **Thumbnail Generation:**
   - Thumbnail is automatically generated at 200x200 pixels
   - Same square crop is applied
   - Image is saved with 85% quality
   - Stored in: `storage/app/public/categories/{uuid}/thumbnails/`

### Manual Upload Steps

1. Navigate to **Admin Panel > Categories**
2. Click **Create Category** or **Edit** an existing category
3. Click **Choose Image** or **Replace Image**
4. Select an image file from your computer
5. The image will be automatically processed and optimized
6. Click **Save** to complete the upload

## Frontend Display

### Category Grid (Homepage)
- **Display Size:** Thumbnail (200x200px)
- **Container:** Square with 1:1 aspect ratio
- **Object Fit:** Cover (ensures image fills container)
- **Location:** `resources/views/frontend/home/index.blade.php`

### Category Detail Page
- **Display Size:** Original (500x500px) or Thumbnail depending on context
- **Container:** Responsive, maintains square aspect ratio
- **Location:** `resources/views/frontend/category/category.blade.php`

## Admin Panel Display

### Category List Table
- **Display Size:** Thumbnail (200x200px)
- **Container:** Square thumbnail preview
- **Location:** `resources/views/admin/category/partials/table.blade.php`

### Category Detail View
- **Display Size:** Original (500x500px)
- **Container:** Large preview with zoom capability
- **Location:** `resources/views/admin/category/show.blade.php`

## Configuration

Image dimensions and processing settings are configured in:
- **File:** `config/images.php`
- **Section:** `category`

```php
'category' => [
    'original' => [
        'width' => 500,
        'height' => 500,
        'quality' => 90,
        'preserve_aspect_ratio' => false,
        'fit' => 'cover',
    ],
    'thumbnail' => [
        'width' => 200,
        'height' => 200,
        'quality' => 85,
        'preserve_aspect_ratio' => false,
        'fit' => 'cover',
    ],
],
```

## Technical Implementation

### Image Service
- **Class:** `App\Services\ImageService`
- **Method:** `uploadImage()` - Handles category image upload and processing
- **Method:** `processCategoryImage()` - Processes category images with specific dimensions
- **Method:** `generateThumbnail()` - Generates category thumbnails

### Controller
- **Class:** `App\Http\Controllers\Admin\Category\CategoryController`
- **Methods:** `store()`, `update()` - Handle image upload during category creation/update

### Storage Structure
```
storage/app/public/
└── categories/
    └── {category-uuid}/
        ├── original/
        │   └── {image-uuid}.jpg
        └── thumbnails/
            └── {image-uuid}.jpg
```

## Best Practices

### Image Selection
1. **Choose Square Source Images:** If possible, start with square images to minimize cropping
2. **Center Important Content:** Place important visual elements in the center of the image
3. **High Resolution:** Use images at least 500x500px for best quality
4. **Consistent Style:** Maintain consistent visual style across all category images

### Image Preparation
1. **Crop Before Upload:** Pre-crop images to square format if possible
2. **Optimize File Size:** Compress images before upload (aim for < 1MB)
3. **Test Display:** Preview how the image looks after upload
4. **Check Thumbnail:** Verify thumbnail looks good in category grid

### Troubleshooting

**Image appears stretched:**
- Ensure source image is close to square aspect ratio
- System will crop to square, so center important content

**Image quality is poor:**
- Use higher resolution source images (minimum 500x500px)
- Check original image quality before upload

**Upload fails:**
- Verify file size is under 2MB
- Check file format is supported (JPEG, PNG, GIF)
- Ensure proper file permissions on storage directory

## Related Documentation

- **Image Service:** `app/Services/ImageService.php`
- **Category Controller:** `app/Http/Controllers/Admin/Category/CategoryController.php`
- **Image Configuration:** `config/images.php`
- **Category Model:** `app/Models/Category.php`

## Changelog

### Version 1.0 (2026-01-12)
- Initial documentation
- Established 500x500px original and 200x200px thumbnail dimensions
- Implemented automatic square cropping for category images
- Added configuration in `config/images.php`

### Version 1.1 (2026-01-12)
- Updated dimensions to 500x500px (original) and 200x200px (thumbnail) for optimal performance
