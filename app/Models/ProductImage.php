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
        return 'assets/images/no-image.png';
    }

    // Override thumbnail fallback for ProductImage
    protected function getThumbnailFallback(): ?string
    {
        return asset('assets/images/no-image.png');
    }
}

