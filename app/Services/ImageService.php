<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    /**
     * Upload image to storage with UUID folder structure and generate thumbnail
     *
     * @param UploadedFile $file The uploaded image file
     * @param string $baseFolder Base folder name (e.g., 'categories', 'products')
     * @param string|null $uuid UUID to use for folder name (from table record)
     * @param string|null $oldImagePath Old image path to delete (optional)
     * @param bool $generateThumbnail Whether to generate thumbnail (default: true)
     * @return string|null The stored image path or null on failure
     */
    public function uploadImage(UploadedFile $file, string $baseFolder, ?string $uuid = null, ?string $oldImagePath = null, bool $generateThumbnail = true): ?string
    {
        try {
            if ($oldImagePath) {
                $this->deleteImage($oldImagePath);
            }

            $folderUuid = $uuid ?? Str::uuid()->toString();

            $originalFolderPath = $baseFolder . '/' . $folderUuid . '/original';
            if (!Storage::disk('public')->exists($originalFolderPath)) {
                Storage::disk('public')->makeDirectory($originalFolderPath, 0755, true);
            }

            $thumbnailsFolderPath = $baseFolder . '/' . $folderUuid . '/thumbnails';
            if (!Storage::disk('public')->exists($thumbnailsFolderPath)) {
                Storage::disk('public')->makeDirectory($thumbnailsFolderPath, 0755, true);
            }

            // Create medium folder for logo, pages, products, bundles, and special-offers
            if (($baseFolder === 'settings' && $uuid === 'logo') || 
                $baseFolder === 'pages' || 
                $baseFolder === 'products' || 
                $baseFolder === 'bundles' || 
                $baseFolder === 'special-offers') {
                $mediumFolderPath = $baseFolder . '/' . $folderUuid . '/medium';
                if (!Storage::disk('public')->exists($mediumFolderPath)) {
                    Storage::disk('public')->makeDirectory($mediumFolderPath, 0755, true);
                }
            }

            $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Use Storage::putFileAs to ensure correct path handling
            $originalImagePath = Storage::disk('public')->putFileAs($originalFolderPath, $file, $imageName);
            
            // Log for debugging
            Log::info('Image uploaded', [
                'baseFolder' => $baseFolder,
                'folderUuid' => $folderUuid,
                'originalFolderPath' => $originalFolderPath,
                'finalImagePath' => $originalImagePath,
                'expectedPath' => $originalFolderPath . '/' . $imageName
            ]);

            // Process category, subcategory, slider, product, bundle, page, about-section, testimonial, special-offers, user, logo, and icon images with specific dimensions
            if ($baseFolder === 'categories' || $baseFolder === 'subcategories') {
                $this->processCategoryImage($originalImagePath);
            } elseif ($baseFolder === 'sliders') {
                $this->processSliderImage($originalImagePath);
            } elseif ($baseFolder === 'products') {
                $this->processProductImage($originalImagePath);
            } elseif ($baseFolder === 'bundles') {
                $this->processProductImage($originalImagePath); // Bundles use same dimensions as products
            } elseif ($baseFolder === 'pages') {
                $this->processPageImage($originalImagePath);
            } elseif ($baseFolder === 'about-sections') {
                $this->processAboutSectionImage($originalImagePath);
            } elseif ($baseFolder === 'testimonials') {
                $this->processTestimonialImage($originalImagePath);
            } elseif ($baseFolder === 'special-offers') {
                $this->processSpecialOffersImage($originalImagePath);
            } elseif ($baseFolder === 'users') {
                $this->processUserImage($originalImagePath);
            } elseif ($baseFolder === 'settings' && ($uuid === 'logo' || strpos($originalImagePath, '/logo/original') !== false)) {
                $this->processLogoImage($originalImagePath);
            } elseif ($baseFolder === 'settings' && ($uuid === 'icon' || strpos($originalImagePath, '/icon/original') !== false)) {
                $this->processIconImage($originalImagePath);
            } else {
                // For other images, resize original if needed
                $this->resizeOriginalImage($originalImagePath, $baseFolder);
            }

            if ($generateThumbnail && config('images.storage.generate_on_upload', true)) {
                if ($baseFolder === 'products' || $baseFolder === 'bundles') {
                    // Generate both medium and thumbnail for products and bundles
                    $this->generateProductMedium($originalImagePath);
                    $this->generateThumbnail($originalImagePath, $baseFolder);
                } elseif ($baseFolder === 'pages') {
                    // Generate both medium and thumbnail for pages
                    $this->generatePageMedium($originalImagePath);
                    $this->generateThumbnail($originalImagePath, $baseFolder);
                } elseif ($baseFolder === 'special-offers') {
                    // Generate both medium and thumbnail for special offers
                    $this->generateSpecialOffersMedium($originalImagePath);
                    $this->generateThumbnail($originalImagePath, $baseFolder);
                } elseif ($baseFolder === 'settings' && ($uuid === 'logo' || strpos($originalImagePath, '/logo/original') !== false)) {
                    // Generate both medium and thumbnail for logo
                    $this->generateLogoMedium($originalImagePath);
                    $this->generateThumbnail($originalImagePath, 'logo');
                } elseif ($baseFolder === 'settings' && ($uuid === 'icon' || strpos($originalImagePath, '/icon/original') !== false)) {
                    // Generate thumbnail for icon
                    $this->generateThumbnail($originalImagePath, 'icon');
                } else {
                    $this->generateThumbnail($originalImagePath, $baseFolder);
                }
            }

            return $originalImagePath;
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'baseFolder' => $baseFolder,
                'uuid' => $uuid,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Process category image with specific dimensions
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processCategoryImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for category processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.category.original', []);
            $width = $config['width'] ?? 800;
            $height = $config['height'] ?? 800;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For category images, scale to cover then crop to exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Category image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process slider image with specific dimensions
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processSliderImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for slider processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.slider.original', []);
            $width = $config['width'] ?? 1920;
            $height = $config['height'] ?? 600;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For slider images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Slider image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process product image with specific dimensions
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processProductImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for product processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.product.original', []);
            $width = $config['width'] ?? 1200;
            $height = $config['height'] ?? 1200;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For product images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Product image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process page image with specific dimensions (1200x400)
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processPageImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for page processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.page.original', []);
            $width = $config['width'] ?? 1200;
            $height = $config['height'] ?? 400;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For page images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Page image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process special offers image with specific dimensions (1920x450)
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processSpecialOffersImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for special offers processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.special-offers.original', []);
            $width = $config['width'] ?? 1920;
            $height = $config['height'] ?? 450;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For special offers images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Special offers image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process testimonial image with specific dimensions (200x200)
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processTestimonialImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for testimonial processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.testimonial.original', []);
            $width = $config['width'] ?? 200;
            $height = $config['height'] ?? 200;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For testimonial images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Testimonial image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process user image with specific dimensions (200x200)
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processUserImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for user processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.user.original', []);
            $width = $config['width'] ?? 200;
            $height = $config['height'] ?? 200;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For user images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('User image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process logo image with specific dimensions (1640x762)
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processLogoImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for logo processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.logo.original', []);
            $width = $config['width'] ?? 1640;
            $height = $config['height'] ?? 762;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For logo images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Logo image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process icon image with specific dimensions (64x64)
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processIconImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for icon processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.icon.original', []);
            $width = $config['width'] ?? 64;
            $height = $config['height'] ?? 64;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For icon images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('Icon image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Process about section image with specific dimensions (600x400)
     *
     * @param string $originalImagePath Path to original image
     * @return bool True on success, false on failure
     */
    protected function processAboutSectionImage(string $originalImagePath): bool
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for about section processing: ' . $originalImagePath);
                return false;
            }

            $config = config('images.about-section.original', []);
            $width = $config['width'] ?? 600;
            $height = $config['height'] ?? 400;
            $quality = $config['quality'] ?? 90;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                // For about section images, use cover fit to ensure exact dimensions
                if ($fit === 'cover') {
                    // Scale to cover the dimensions (maintains aspect ratio, may crop)
                    $image->scale(width: $width, height: $height);
                    // Crop to exact dimensions from center
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($originalFullPath);

            return true;
        } catch (\Exception $e) {
            Log::error('About section image processing failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Generate medium size image for products (600x600)
     *
     * @param string $originalImagePath Path to original image
     * @return string|null Medium image path or null on failure
     */
    protected function generateProductMedium(string $originalImagePath): ?string
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for product medium generation: ' . $originalImagePath);
                return null;
            }

            $config = config('images.product.medium', []);
            $width = $config['width'] ?? 600;
            $height = $config['height'] ?? 600;
            $quality = $config['quality'] ?? 85;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            // Generate medium path (replace /original/ with /medium/)
            $mediumPath = str_replace('/original/', '/medium/', $originalImagePath);
            $mediumDir = dirname($mediumPath);
            
            if (!Storage::disk('public')->exists($mediumDir)) {
                Storage::disk('public')->makeDirectory($mediumDir, 0755, true);
            }

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $mediumFullPath = Storage::disk('public')->path($mediumPath);

            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                if ($fit === 'cover') {
                    $image->scale(width: $width, height: $height);
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($mediumFullPath);

            return $mediumPath;
        } catch (\Exception $e) {
            Log::error('Product medium image generation failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Generate medium size image for special offers (960x225)
     *
     * @param string $originalImagePath Path to original image
     * @return string|null Medium image path or null on failure
     */
    protected function generateSpecialOffersMedium(string $originalImagePath): ?string
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for special offers medium generation: ' . $originalImagePath);
                return null;
            }

            $config = config('images.special-offers.medium', []);
            $width = $config['width'] ?? 960;
            $height = $config['height'] ?? 225;
            $quality = $config['quality'] ?? 85;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            // Generate medium path (replace /original/ with /medium/)
            $mediumPath = str_replace('/original/', '/medium/', $originalImagePath);
            $mediumDir = dirname($mediumPath);
            
            if (!Storage::disk('public')->exists($mediumDir)) {
                Storage::disk('public')->makeDirectory($mediumDir, 0755, true);
            }

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $mediumFullPath = Storage::disk('public')->path($mediumPath);

            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                if ($fit === 'cover') {
                    $image->scale(width: $width, height: $height);
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($mediumFullPath);

            return $mediumPath;
        } catch (\Exception $e) {
            Log::error('Special offers medium image generation failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Generate medium-sized image for logo (410x190)
     *
     * @param string $originalImagePath Path to original image
     * @return string|null Path to medium image or null on failure
     */
    protected function generateLogoMedium(string $originalImagePath): ?string
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for logo medium generation: ' . $originalImagePath);
                return null;
            }

            $config = config('images.logo.medium', []);
            $width = $config['width'] ?? 410;
            $height = $config['height'] ?? 190;
            $quality = $config['quality'] ?? 85;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            // Generate medium path (replace /original/ with /medium/)
            $mediumPath = str_replace('/original/', '/medium/', $originalImagePath);
            $mediumDir = dirname($mediumPath);

            if (!Storage::disk('public')->exists($mediumDir)) {
                Storage::disk('public')->makeDirectory($mediumDir, 0755, true);
            }

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $mediumFullPath = Storage::disk('public')->path($mediumPath);

            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                if ($fit === 'cover') {
                    $image->scale(width: $width, height: $height);
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($mediumFullPath);

            return $mediumPath;
        } catch (\Exception $e) {
            Log::error('Logo medium generation failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Generate medium size image for pages (600x200)
     *
     * @param string $originalImagePath Path to original image
     * @return string|null Medium image path or null on failure
     */
    protected function generatePageMedium(string $originalImagePath): ?string
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for page medium generation: ' . $originalImagePath);
                return null;
            }

            $config = config('images.page.medium', []);
            $width = $config['width'] ?? 600;
            $height = $config['height'] ?? 200;
            $quality = $config['quality'] ?? 85;
            $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
            $fit = $config['fit'] ?? 'cover';

            // Generate medium path (replace /original/ with /medium/)
            $mediumPath = str_replace('/original/', '/medium/', $originalImagePath);
            $mediumDir = dirname($mediumPath);
            
            if (!Storage::disk('public')->exists($mediumDir)) {
                Storage::disk('public')->makeDirectory($mediumDir, 0755, true);
            }

            $originalFullPath = Storage::disk('public')->path($originalImagePath);
            $mediumFullPath = Storage::disk('public')->path($mediumPath);

            $manager = new ImageManager(new Driver());
            $image = $manager->read($originalFullPath);

            if ($preserveAspectRatio) {
                $image->scale(width: $width, height: $height);
            } else {
                if ($fit === 'cover') {
                    $image->scale(width: $width, height: $height);
                    $image->crop($width, $height);
                } else {
                    $image->resize($width, $height);
                }
            }

            $image->toJpeg($quality)->save($mediumFullPath);

            return $mediumPath;
        } catch (\Exception $e) {
            Log::error('Page medium image generation failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Resize original image if needed (for non-category/slider/product images)
     *
     * @param string $originalImagePath Path to original image
     * @param string $baseFolder Base folder name
     * @return bool True on success, false on failure
     */
    protected function resizeOriginalImage(string $originalImagePath, string $baseFolder): bool
    {
        // For now, keep original size for other images
        // Can be extended later if needed
        return true;
    }

    /**
     * Generate thumbnail from original image
     *
     * @param string $originalImagePath Path to original image
     * @param string|null $baseFolder Base folder name to determine image type
     * @return string|null Thumbnail path or null on failure
     */
    public function generateThumbnail(string $originalImagePath, ?string $baseFolder = null): ?string
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for thumbnail generation: ' . $originalImagePath);
                return null;
            }

            // Use category-specific settings if it's a category image
            if ($baseFolder === 'categories' || $baseFolder === 'subcategories') {
                $config = config('images.category.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 400;
                $thumbnailHeight = $config['height'] ?? 400;
                $quality = $config['quality'] ?? 85;
                $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
                $fit = $config['fit'] ?? 'cover';
            } elseif ($baseFolder === 'sliders') {
                $config = config('images.slider.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 400;
                $thumbnailHeight = $config['height'] ?? 400;
                $quality = $config['quality'] ?? 85;
                $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? true;
                $fit = $config['fit'] ?? 'contain';
            } elseif ($baseFolder === 'products' || $baseFolder === 'bundles') {
                $config = config('images.product.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 300;
                $thumbnailHeight = $config['height'] ?? 300;
                $quality = $config['quality'] ?? 85;
                $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
                $fit = $config['fit'] ?? 'cover';
            } elseif ($baseFolder === 'pages') {
                $config = config('images.page.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 300;
                $thumbnailHeight = $config['height'] ?? 100;
                $quality = $config['quality'] ?? 85;
                $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
                $fit = $config['fit'] ?? 'cover';
            } elseif ($baseFolder === 'about-sections') {
                $config = config('images.about-section.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 300;
                $thumbnailHeight = $config['height'] ?? 200;
                $quality = $config['quality'] ?? 85;
            } elseif ($baseFolder === 'logo') {
                $config = config('images.logo.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 300;
                $thumbnailHeight = $config['height'] ?? 140;
                $quality = $config['quality'] ?? 85;
                $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
                $fit = $config['fit'] ?? 'cover';
            } elseif ($baseFolder === 'icon') {
                $config = config('images.icon.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 32;
                $thumbnailHeight = $config['height'] ?? 32;
                $quality = $config['quality'] ?? 85;
                $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
                $fit = $config['fit'] ?? 'cover';
            } elseif ($baseFolder === 'testimonials') {
                $config = config('images.testimonial.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 100;
                $thumbnailHeight = $config['height'] ?? 100;
                $quality = $config['quality'] ?? 85;
                $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
                $fit = $config['fit'] ?? 'cover';
            } elseif ($baseFolder === 'special-offers') {
                $config = config('images.special-offers.thumbnail', []);
                $thumbnailWidth = $config['width'] ?? 480;
                $thumbnailHeight = $config['height'] ?? 112;
                $quality = $config['quality'] ?? 85;
                $preserveAspectRatio = $config['preserve_aspect_ratio'] ?? false;
                $fit = $config['fit'] ?? 'cover';
            } else {
                $thumbnailWidth = config('images.thumbnail.width', 400);
                $thumbnailHeight = config('images.thumbnail.height', 400);
                $quality = config('images.thumbnail.quality', 85);
                $preserveAspectRatio = config('images.thumbnail.preserve_aspect_ratio', true);
                $fit = 'contain';
            }

            $thumbnailPath = $this->getThumbnailPath($originalImagePath);

            $thumbnailDir = dirname($thumbnailPath);
            if (!Storage::disk('public')->exists($thumbnailDir)) {
                Storage::disk('public')->makeDirectory($thumbnailDir, 0755, true);
            }

            $originalFullPath = Storage::disk('public')->path($originalImagePath);

            $manager = new ImageManager(new Driver());

            $image = $manager->read($originalFullPath);

            if (($baseFolder === 'categories' || $baseFolder === 'subcategories' || $baseFolder === 'products' || $baseFolder === 'bundles' || $baseFolder === 'pages' || $baseFolder === 'about-sections' || $baseFolder === 'testimonials' || $baseFolder === 'special-offers' || $baseFolder === 'logo' || $baseFolder === 'icon') && $fit === 'cover' && !$preserveAspectRatio) {
                // For category/subcategory/product/bundle/page/about-section/testimonial/special-offers/logo/icon thumbnails, scale to cover then crop to exact dimensions
                $image->scale(width: $thumbnailWidth, height: $thumbnailHeight);
                $image->crop($thumbnailWidth, $thumbnailHeight);
            } elseif ($preserveAspectRatio) {
                $image->scale(width: $thumbnailWidth, height: $thumbnailHeight);
            } else {
                $image->resize($thumbnailWidth, $thumbnailHeight);
            }

            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

            $image->toJpeg($quality)->save($thumbnailFullPath);

            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get thumbnail path from original image path
     *
     * @param string $originalImagePath Original image path
     * @return string Thumbnail path
     */
    public function getThumbnailPath(string $originalImagePath): string
    {
        if (strpos($originalImagePath, '/original/') !== false) {
            return str_replace('/original/', '/thumbnails/', $originalImagePath);
        }

        $pathParts = explode('/', $originalImagePath);
        $fileName = array_pop($pathParts);
        $basePath = implode('/', $pathParts);

        return $basePath . '/thumbnails/' . $fileName;
    }

    /**
     * Get thumbnail URL from original image path
     *
     * @param string|null $originalImagePath Original image path
     * @return string|null Thumbnail URL or null
     */
    public function getThumbnailUrl(?string $originalImagePath): ?string
    {
        if (!$originalImagePath) {
            return null;
        }

        $thumbnailPath = $this->getThumbnailPath($originalImagePath);

        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        return $this->getImageUrl($originalImagePath);
    }

    /**
     * Simple upload without UUID folder structure (for settings, simple files)
     *
     * @param UploadedFile $file The uploaded image file
     * @param string $folder Folder name (e.g., 'settings', 'avatars')
     * @param string|null $oldImagePath Old image path to delete (optional)
     * @return string|null The stored image path or null on failure
     */
    public function uploadSimple(UploadedFile $file, string $folder, ?string $oldImagePath = null): ?string
    {
        try {
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            $imagePath = $file->storeAs($folder, $imageName, 'public');

            return $imagePath;
        } catch (\Exception $e) {
            Log::error('Simple image upload failed: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'folder' => $folder,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Delete image and its thumbnail
     *
     * @param string $imagePath The image path to delete (original path)
     * @return bool True on success, false on failure
     */
    public function deleteImage(?string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }

        try {
            $deleted = false;

            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
                $deleted = true;
            }

            $thumbnailPath = $this->getThumbnailPath($imagePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            if (strpos($imagePath, '/original/') !== false) {
                $folderPath = dirname(dirname($imagePath));
                $originalFolder = $folderPath . '/original';
                $thumbnailsFolder = $folderPath . '/thumbnails';

                $originalEmpty = !Storage::disk('public')->exists($originalFolder) ||
                               count(Storage::disk('public')->files($originalFolder)) === 0;
                $thumbnailsEmpty = !Storage::disk('public')->exists($thumbnailsFolder) ||
                                  count(Storage::disk('public')->files($thumbnailsFolder)) === 0;

                if ($originalEmpty && $thumbnailsEmpty && Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                }
            } else {
                $folderPath = dirname($imagePath);
                if (Storage::disk('public')->exists($folderPath)) {
                    $files = Storage::disk('public')->files($folderPath);
                    if (count($files) === 0) {
                        Storage::disk('public')->deleteDirectory($folderPath);
                    }
                }
            }

            return $deleted;
        } catch (\Exception $e) {
            Log::error('Image delete failed: ' . $e->getMessage(), [
                'image_path' => $imagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Update image (delete old and upload new)
     *
     * @param UploadedFile $file The new uploaded image file
     * @param string $baseFolder Base folder name
     * @param string|null $uuid UUID to use for folder name (from table record)
     * @param string|null $oldImagePath Old image path to delete
     * @return string|null The new stored image path or null on failure
     */
    public function updateImage(UploadedFile $file, string $baseFolder, ?string $uuid = null, ?string $oldImagePath = null): ?string
    {
        return $this->uploadImage($file, $baseFolder, $uuid, $oldImagePath);
    }

    /**
     * Get image URL from storage path
     *
     * @param string|null $imagePath The storage path
     * @return string|null The public URL or null
     */
    public function getImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }

        return $imagePath ? asset('storage/' . $imagePath) : null;
    }

    /**
     * Regenerate thumbnails for an existing image
     *
     * @param string $originalImagePath Original image path
     * @param string|null $baseFolder Base folder name to determine image type
     * @return bool True on success, false on failure
     */
    public function regenerateThumbnail(string $originalImagePath, ?string $baseFolder = null): bool
    {
        try {
            $thumbnailPath = $this->getThumbnailPath($originalImagePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            // Determine base folder from path if not provided
            if (!$baseFolder && strpos($originalImagePath, '/') !== false) {
                $pathParts = explode('/', $originalImagePath);
                $baseFolder = $pathParts[0] ?? null;
            }

            $newThumbnailPath = $this->generateThumbnail($originalImagePath, $baseFolder);

            return $newThumbnailPath !== null;
        } catch (\Exception $e) {
            Log::error('Thumbnail regeneration failed: ' . $e->getMessage(), [
                'original_path' => $originalImagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
