<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class GalleryItem extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'gallery_id',
        'type',
        'title',
        'description',
        'image_path',
        'video_embed_code',
        'video_url',
        'thumbnail_path',
        'order',
        'is_featured',
        'alt_text',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'order' => 'integer',
    ];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        return asset('storage/' . $this->image_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }
        if ($this->image_path) {
            $path = str_replace('/original/', '/thumbnails/', $this->image_path);
            if (file_exists(public_path('storage/' . $path))) {
                return asset('storage/' . $path);
            }
        }
        return $this->image_url;
    }
}
