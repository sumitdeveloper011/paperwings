<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class ProductAnswer extends Model
{
    use HasFactory, HasUuid;

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


    // Get the question relationship
    public function question(): BelongsTo
    {
        return $this->belongsTo(ProductQuestion::class, 'question_id');
    }

    // Get the user relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
