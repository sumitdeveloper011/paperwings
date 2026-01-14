<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use App\Traits\HasImageUrl;
use App\Traits\HasThumbnail;
use App\Traits\HasUniqueSlug;

class Page extends Model
{
    use HasFactory, HasUuid, HasImageUrl, HasThumbnail, HasUniqueSlug;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'sub_title',
        'content',
        'image',
        'status',
    ];

    // Boot method to generate UUID and slug automatically
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($page) {
            if ($page->isDirty('title') && !$page->isDirty('slug')) {
                $page->slug = static::makeUniqueSlug($page->title, $page->id);
            }
        });
    }

    // Override fallback image for Page
    protected function getFallbackImage(): string
    {
        return 'assets/images/placeholder.jpg';
    }

    // Override thumbnail fallback for Page
    protected function getThumbnailFallback(): ?string
    {
        return asset('assets/images/placeholder.jpg');
    }

    /**
     * Get medium image path attribute
     *
     * @return string|null
     */
    public function getMediumPathAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // Check if path has /original/ folder structure
        if (strpos($this->image, '/original/') !== false) {
            // Replace /original/ with /medium/
            return str_replace('/original/', '/medium/', $this->image);
        }

        // For old structure (backward compatibility)
        $pathParts = explode('/', $this->image);
        $fileName = array_pop($pathParts);
        $basePath = implode('/', $pathParts);

        return $basePath . '/medium/' . $fileName;
    }

    /**
     * Get medium image URL attribute
     *
     * @return string|null
     */
    public function getMediumUrlAttribute(): ?string
    {
        $mediumPath = $this->medium_path;

        if (!$mediumPath) {
            return $this->image_url;
        }

        // Check if medium exists
        if (Storage::disk('public')->exists($mediumPath)) {
            return asset('storage/' . $mediumPath);
        }

        // Fallback to original if medium doesn't exist
        return $this->image_url;
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
