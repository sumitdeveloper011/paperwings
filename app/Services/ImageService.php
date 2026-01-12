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
            // Delete old image and thumbnails if exists
            if ($oldImagePath) {
                $this->deleteImage($oldImagePath);
            }

            // Use provided UUID or generate new one
            $folderUuid = $uuid ?? Str::uuid()->toString();

            // Create folder structure: {baseFolder}/{uuid}/original
            $originalFolderPath = $baseFolder . '/' . $folderUuid . '/original';
            if (!Storage::disk('public')->exists($originalFolderPath)) {
                Storage::disk('public')->makeDirectory($originalFolderPath, 0755, true);
            }

            // Also create thumbnails folder structure for future use
            $thumbnailsFolderPath = $baseFolder . '/' . $folderUuid . '/thumbnails';
            if (!Storage::disk('public')->exists($thumbnailsFolderPath)) {
                Storage::disk('public')->makeDirectory($thumbnailsFolderPath, 0755, true);
            }

            // Generate unique image name
            $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store original image
            $originalImagePath = $file->storeAs($originalFolderPath, $imageName, 'public');

            // Generate thumbnail if enabled
            if ($generateThumbnail && config('images.storage.generate_on_upload', true)) {
                $this->generateThumbnail($originalImagePath);
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
     * Generate thumbnail from original image
     *
     * @param string $originalImagePath Path to original image
     * @return string|null Thumbnail path or null on failure
     */
    public function generateThumbnail(string $originalImagePath): ?string
    {
        try {
            if (!Storage::disk('public')->exists($originalImagePath)) {
                Log::warning('Original image not found for thumbnail generation: ' . $originalImagePath);
                return null;
            }

            // Get thumbnail settings from config
            $thumbnailWidth = config('images.thumbnail.width', 400);
            $thumbnailHeight = config('images.thumbnail.height', 400);
            $quality = config('images.thumbnail.quality', 85);
            $preserveAspectRatio = config('images.thumbnail.preserve_aspect_ratio', true);

            // Generate thumbnail path: products/{uuid}/original/image.jpg -> products/{uuid}/thumbnails/image.jpg
            $thumbnailPath = $this->getThumbnailPath($originalImagePath);

            // Create thumbnails directory if not exists
            $thumbnailDir = dirname($thumbnailPath);
            if (!Storage::disk('public')->exists($thumbnailDir)) {
                Storage::disk('public')->makeDirectory($thumbnailDir, 0755, true);
            }

            // Get full path to original image
            $originalFullPath = Storage::disk('public')->path($originalImagePath);

            // Create image manager
            $manager = new ImageManager(new Driver());

            // Read and process image
            $image = $manager->read($originalFullPath);

            // Strip EXIF data if enabled (Intervention Image v3 handles this automatically on save)
            // EXIF data is removed when encoding to JPEG

            // Resize image
            if ($preserveAspectRatio) {
                // Scale maintaining aspect ratio
                $image->scale(width: $thumbnailWidth, height: $thumbnailHeight);
            } else {
                // Resize to exact dimensions
                $image->resize(width: $thumbnailWidth, height: $thumbnailHeight);
            }

            // Get thumbnail full path
            $thumbnailFullPath = Storage::disk('public')->path($thumbnailPath);

            // Save thumbnail with quality
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
        // Check if path already has /original/ folder structure
        if (strpos($originalImagePath, '/original/') !== false) {
            // Replace /original/ with /thumbnails/
            return str_replace('/original/', '/thumbnails/', $originalImagePath);
        }

        // For old structure (backward compatibility): products/{uuid}/image.jpg
        // Convert to: products/{uuid}/thumbnails/image.jpg
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

        // Check if thumbnail exists, if not return original
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Fallback to original if thumbnail doesn't exist
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
            // Delete old image if exists
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            // Generate unique image name
            $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store image directly in folder
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

            // Delete original image
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
                $deleted = true;
            }

            // Delete thumbnail if exists (works for both old and new structure)
            $thumbnailPath = $this->getThumbnailPath($imagePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            // Handle folder cleanup for new structure (with /original/ folder)
            if (strpos($imagePath, '/original/') !== false) {
                $folderPath = dirname(dirname($imagePath));
                $originalFolder = $folderPath . '/original';
                $thumbnailsFolder = $folderPath . '/thumbnails';

                $originalEmpty = !Storage::disk('public')->exists($originalFolder) ||
                               count(Storage::disk('public')->files($originalFolder)) === 0;
                $thumbnailsEmpty = !Storage::disk('public')->exists($thumbnailsFolder) ||
                                  count(Storage::disk('public')->files($thumbnailsFolder)) === 0;

                // If both folders are empty, delete the parent folder
                if ($originalEmpty && $thumbnailsEmpty && Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                }
            } else {
                // Handle old structure: delete folder if empty
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

        // Use asset helper to generate public URL
        return $imagePath ? asset('storage/' . $imagePath) : null;
    }

    /**
     * Regenerate thumbnails for an existing image
     *
     * @param string $originalImagePath Original image path
     * @return bool True on success, false on failure
     */
    public function regenerateThumbnail(string $originalImagePath): bool
    {
        try {
            // Delete existing thumbnail if exists
            $thumbnailPath = $this->getThumbnailPath($originalImagePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            // Generate new thumbnail
            $newThumbnailPath = $this->generateThumbnail($originalImagePath);

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
