<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasUuid;
use App\Traits\HasUniqueSlug;

class Gallery extends Model
{
    use HasFactory, HasUuid, HasUniqueSlug;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'category',
        'status',
        'cover_image_id',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)->orderBy('order');
    }

    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(GalleryItem::class, 'cover_image_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }
}
