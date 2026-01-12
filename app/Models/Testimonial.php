<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'review',
        'rating',
        'image',
        'designation',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'rating' => 'integer',
        'status' => 'integer',
        'sort_order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($testimonial) {
            if (empty($testimonial->uuid)) {
                $testimonial->uuid = Str::uuid();
            }

            if (is_null($testimonial->sort_order)) {
                $maxOrder = static::max('sort_order') ?? 0;
                $testimonial->sort_order = $maxOrder + 1;
            }
        });
    }

    // Scope to filter active testimonials
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter inactive testimonials
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Scope to order testimonials
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get image URL attribute (original)
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/images/profile.png');
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
            return asset('assets/images/profile.png');
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
