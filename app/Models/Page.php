<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

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

        static::creating(function ($page) {
            if (empty($page->uuid)) {
                $page->uuid = Str::uuid();
            }
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && empty($page->getOriginal('slug'))) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    // Get image URL attribute (original)
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/images/placeholder.jpg');
    }

    // Get thumbnail path attribute
    public function getThumbnailPathAttribute()
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

    // Get thumbnail URL attribute
    public function getThumbnailUrlAttribute()
    {
        if (!$this->image) {
            return asset('assets/images/placeholder.jpg');
        }

        $thumbnailPath = $this->thumbnail_path;

        // Check if thumbnail exists
        if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Fallback to original if thumbnail doesn't exist
        return $this->image_url;
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
