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

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
