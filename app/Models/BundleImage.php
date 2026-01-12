<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BundleImage extends Model
{
    use HasFactory;

    protected $table = 'bundle_images';

    protected $fillable = [
        'uuid',
        'bundle_id',
        'image',
    ];

    // Boot method to generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (empty($image->uuid)) {
                $image->uuid = Str::uuid();
            }
        });
    }

    // Get the bundle relationship
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(ProductBundle::class, 'bundle_id');
    }

    // Get the image URL attribute (original)
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/'.$this->image) : asset('assets/images/no-image.png');
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
            return asset('assets/images/no-image.png');
        }

        $thumbnailPath = $this->thumbnail_path;

        // Check if thumbnail exists
        if ($thumbnailPath && Storage::disk('public')->exists($thumbnailPath)) {
            return asset('storage/' . $thumbnailPath);
        }

        // Fallback to original if thumbnail doesn't exist
        return $this->image_url;
    }
}
