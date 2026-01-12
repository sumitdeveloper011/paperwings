<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasUuid;
use App\Traits\HasImageUrl;
use App\Traits\HasThumbnail;

class AboutSection extends Model
{
    use HasFactory, HasUuid, HasImageUrl, HasThumbnail;

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

    // Override fallback image for AboutSection
    protected function getFallbackImage(): string
    {
        return 'assets/frontend/images/about-us.jpg';
    }

    // Override thumbnail fallback for AboutSection
    protected function getThumbnailFallback(): ?string
    {
        return asset('assets/frontend/images/about-us.jpg');
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
