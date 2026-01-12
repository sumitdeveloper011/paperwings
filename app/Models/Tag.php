<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use App\Traits\HasUniqueSlug;

class Tag extends Model
{
    use HasFactory, HasUuid, HasUniqueSlug;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && !$tag->isDirty('slug')) {
                $tag->slug = static::makeUniqueSlug($tag->name, $tag->id);
            }
        });
    }

    // Get the products relationship
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
