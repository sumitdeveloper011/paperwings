<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasUuid;
use App\Traits\HasImageUrl;
use App\Traits\HasThumbnail;

class ProductImage extends Model
{
    use HasFactory, HasUuid, HasImageUrl, HasThumbnail;

    protected $table = 'products_images';

    protected $fillable = [
        'uuid',
        'product_id',
        'eposnow_product_id',
        'image',
    ];

    // Get the product relationship
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Override fallback image for ProductImage
    protected function getFallbackImage(): string
    {
        return 'assets/images/placeholder.jpg';
    }

    // Override thumbnail fallback for ProductImage
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
}

