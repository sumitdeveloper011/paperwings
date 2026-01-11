<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    // Get the image URL attribute
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/'.$this->image) : asset('assets/images/no-image.png');
    }
}
