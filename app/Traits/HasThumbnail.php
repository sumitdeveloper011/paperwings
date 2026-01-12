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
        if (!$this->image) {
            return null;
        }

        // Check if path has /original/ folder structure
        if (strpos($this->image, '/original/') !== false) {
            // Replace /original/ with /thumbnails/
            return str_replace('/original/', '/thumbnails/', $this->image);
        }

        // For old structure (backward compatibility)
        $pathParts = explode('/', $this->image);
        $fileName = array_pop($pathParts);
        $basePath = implode('/', $pathParts);

        return $basePath . '/thumbnails/' . $fileName;
    }

    /**
     * Get thumbnail URL attribute
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        $thumbnailPath = $this->thumbnail_path;

        if (!$thumbnailPath) {
            return $this->getThumbnailFallback();
        }

        // Check if thumbnail exists
        if (Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Fallback to original if thumbnail doesn't exist
        return $this->image_url ?? $this->getThumbnailFallback();
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
