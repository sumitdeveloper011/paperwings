<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Upload image to storage with UUID folder structure
     *
     * @param UploadedFile $file The uploaded image file
     * @param string $baseFolder Base folder name (e.g., 'categories', 'products')
     * @param string|null $uuid UUID to use for folder name (from table record)
     * @param string|null $oldImagePath Old image path to delete (optional)
     * @return string|null The stored image path or null on failure
     */
    public function uploadImage(UploadedFile $file, string $baseFolder, ?string $uuid = null, ?string $oldImagePath = null): ?string
    {
        try {
            // Delete old image and folder if exists
            if ($oldImagePath) {
                $this->deleteImage($oldImagePath);
            }

            // Use provided UUID or generate new one
            $folderUuid = $uuid ?? Str::uuid()->toString();

            // Check if folder exists, if not create it
            $folderPath = $baseFolder . '/' . $folderUuid;
            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }

            // Generate unique image name
            $imageName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Store image in: {baseFolder}/{uuid}/{imageName}
            $imagePath = $file->storeAs($folderPath, $imageName, 'public');

            return $imagePath;
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete image and its folder
     *
     * @param string $imagePath The image path to delete
     * @return bool True on success, false on failure
     */
    public function deleteImage(?string $imagePath): bool
    {
        if (!$imagePath) {
            return false;
        }

        try {
            // Check if image exists
            if (Storage::disk('public')->exists($imagePath)) {
                // Delete the image file
                Storage::disk('public')->delete($imagePath);

                // Get folder path
                $folderPath = dirname($imagePath);

                // Delete the entire folder and all its contents
                if (Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                }

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Image delete failed: ' . $e->getMessage());
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
}
