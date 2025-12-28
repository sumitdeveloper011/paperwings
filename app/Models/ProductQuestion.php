<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'product_id',
        'user_id',
        'name',
        'email',
        'question',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productQuestion) {
            if (empty($productQuestion->uuid)) {
                $productQuestion->uuid = \Illuminate\Support\Str::uuid();
            }
        });
    }

    // Get the product relationship
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Get the user relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Get the answers relationship
    public function answers(): HasMany
    {
        return $this->hasMany(ProductAnswer::class, 'question_id');
    }

    // Get the approved answers relationship
    public function approvedAnswers(): HasMany
    {
        return $this->hasMany(ProductAnswer::class, 'question_id')->where('status', 1);
    }

    // Scope to filter approved questions
    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter pending questions
    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    // Get reviewer name attribute
    public function getReviewerNameAttribute()
    {
        return $this->user ? $this->user->name : $this->name;
    }

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
