<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasUuid;
use App\Traits\HasImageUrl;
use App\Traits\HasThumbnail;

class SpecialOffersBanner extends Model
{
    use HasFactory, HasUuid, HasImageUrl, HasThumbnail;

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

    // Override fallback image for SpecialOffersBanner
    protected function getFallbackImage(): string
    {
        return 'assets/images/placeholder.jpg';
    }

    // Override thumbnail fallback for SpecialOffersBanner
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
