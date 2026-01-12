<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasUuid;
use App\Traits\HasImageUrl;
use App\Traits\HasThumbnail;

class Testimonial extends Model
{
    use HasFactory, HasUuid, HasImageUrl, HasThumbnail;

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

    // Override fallback image for Testimonial
    protected function getFallbackImage(): string
    {
        return 'assets/images/profile.png';
    }

    // Override thumbnail fallback for Testimonial
    protected function getThumbnailFallback(): ?string
    {
        return asset('assets/images/profile.png');
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
