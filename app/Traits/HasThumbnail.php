<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasThumbnail
{
    /**
     * Get thumbnail path attribute
     *
     * @return string|null
     */
    public function getThumbnailPathAttribute(): ?string
    {
        if (!$this->image || $this->image === '') {
            return null;
        }

        // Check if path has /original/ folder structure (new structure)
        if (strpos($this->image, '/original/') !== false) {
            // Replace /original/ with /thumbnails/
            return str_replace('/original/', '/thumbnails/', $this->image);
        }

        // For old structure (backward compatibility) - if path doesn't have /original/
        // Try to find the base path and add /thumbnails/
        $pathParts = explode('/', $this->image);
        if (count($pathParts) > 1) {
            $fileName = array_pop($pathParts);
            $basePath = implode('/', $pathParts);
            return $basePath . '/thumbnails/' . $fileName;
        }

        // If path structure is unclear, return null (will fallback to original or placeholder)
        return null;
    }

    /**
     * Get thumbnail URL attribute
     *
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        // If no image, return placeholder
        if (!$this->image || $this->image === '') {
            $fallback = $this->getThumbnailFallback();
            return $fallback ?: asset('assets/images/placeholder.jpg');
        }

        // Generate thumbnail path
        $thumbnailPath = $this->thumbnail_path;

        // If thumbnail path can't be generated, use original as fallback
        if (!$thumbnailPath || $thumbnailPath === '') {
            // Check if original exists
            if (Storage::disk('public')->exists($this->image)) {
                return $this->image_url;
            }
            // Return placeholder if original doesn't exist
            $fallback = $this->getThumbnailFallback();
            return $fallback ?: asset('assets/images/placeholder.jpg');
        }

        // Check if thumbnail file exists in storage
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Thumbnail doesn't exist, fallback to original if it exists
        if (Storage::disk('public')->exists($this->image)) {
            return $this->image_url;
        }

        // Neither thumbnail nor original exists, return placeholder
        $fallback = $this->getThumbnailFallback();
        return $fallback ?: asset('assets/images/placeholder.jpg');
    }

    /**
     * Get fallback for thumbnail (can be overridden in model)
     *
     * @return string|null
     */
    protected function getThumbnailFallback(): ?string
    {
        return null;
    }
}
