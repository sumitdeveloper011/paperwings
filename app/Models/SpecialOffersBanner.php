<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpecialOffersBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'image',
        'button_text',
        'button_link',
        'start_date',
        'end_date',
        'show_countdown',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'show_countdown' => 'boolean',
        'status' => 'integer',
        'sort_order' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($banner) {
            if (empty($banner->uuid)) {
                $banner->uuid = Str::uuid();
            }

            if (is_null($banner->sort_order)) {
                $maxOrder = static::max('sort_order') ?? 0;
                $banner->sort_order = $maxOrder + 1;
            }
        });
    }

    // Scope to filter active banners
    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            });
    }

    // Scope to filter inactive banners
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Scope to order banners
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get image URL attribute (original)
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
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
            return null;
        }

        $thumbnailPath = $this->thumbnail_path;

        // Check if thumbnail exists
        if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Fallback to original if thumbnail doesn't exist
        return $this->image_url;
    }

    // Get is active attribute
    public function getIsActiveAttribute()
    {
        if ($this->status != 1) {
            return false;
        }

        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        return true;
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
