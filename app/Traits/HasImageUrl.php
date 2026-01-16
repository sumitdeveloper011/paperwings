<?php

namespace App\Traits;

trait HasImageUrl
{
    /**
     * Get image URL attribute with fallback
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset($this->getFallbackImage());
        }

        return asset('storage/' . $this->image);
    }

    /**
     * Get fallback image path (can be overridden in model)
     *
     * @return string
     */
    protected function getFallbackImage(): string
    {
        return 'assets/images/placeholder.jpg'; // Default fallback
    }
}
