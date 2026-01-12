<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AboutSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'badge',
        'title',
        'description',
        'button_text',
        'button_link',
        'image',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer'
    ];

    // Boot method to generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aboutSection) {
            if (empty($aboutSection->uuid)) {
                $aboutSection->uuid = Str::uuid();
            }

            // Set default sort_order to 0 for single entry
            if (is_null($aboutSection->sort_order)) {
                $aboutSection->sort_order = 0;
            }
        });
    }

    // Scope to filter active about sections
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to order about sections
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get image URL attribute (original)
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/frontend/images/about-us.jpg');
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
            return asset('assets/frontend/images/about-us.jpg');
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
