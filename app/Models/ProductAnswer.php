<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'question_id',
        'user_id',
        'name',
        'answer',
        'helpful_count',
        'status',
    ];

    protected $casts = [
        'helpful_count' => 'integer',
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($answer) {
            if (empty($answer->uuid)) {
                $answer->uuid = Str::uuid();
            }
        });
    }

    // Get the question relationship
    public function question(): BelongsTo
    {
        return $this->belongsTo(ProductQuestion::class, 'question_id');
    }

    // Get the user relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // Scope to filter approved answers
    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    // Get reviewer name attribute
    public function getReviewerNameAttribute()
    {
        return $this->user ? $this->user->name : $this->name;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
