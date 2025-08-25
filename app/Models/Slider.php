<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'image',
        'heading',
        'sub_heading',
        'buttons',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'buttons' => 'array',
        'sort_order' => 'integer',
        'status' => 'string'
    ];

    // Boot method to generate UUID automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($slider) {
            if (empty($slider->uuid)) {
                $slider->uuid = Str::uuid();
            }
            
            // Auto-set sort order if not provided
            if (is_null($slider->sort_order)) {
                $maxOrder = static::max('sort_order') ?? 0;
                $slider->sort_order = $maxOrder + 1;
            }
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'active' 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/images/no-image.png');
    }

    public function getButtonCountAttribute()
    {
        return $this->buttons ? count($this->buttons) : 0;
    }

    public function getFirstButtonAttribute()
    {
        return $this->buttons && count($this->buttons) > 0 ? $this->buttons[0] : null;
    }

    public function getSecondButtonAttribute()
    {
        return $this->buttons && count($this->buttons) > 1 ? $this->buttons[1] : null;
    }

    public function getHasButtonsAttribute()
    {
        return $this->buttons && count($this->buttons) > 0;
    }

    // Methods
    public function moveUp()
    {
        $previousSlider = static::where('sort_order', '<', $this->sort_order)
                               ->orderBy('sort_order', 'desc')
                               ->first();

        if ($previousSlider) {
            $tempOrder = $this->sort_order;
            $this->sort_order = $previousSlider->sort_order;
            $previousSlider->sort_order = $tempOrder;

            $this->save();
            $previousSlider->save();
        }
    }

    public function moveDown()
    {
        $nextSlider = static::where('sort_order', '>', $this->sort_order)
                           ->orderBy('sort_order', 'asc')
                           ->first();

        if ($nextSlider) {
            $tempOrder = $this->sort_order;
            $this->sort_order = $nextSlider->sort_order;
            $nextSlider->sort_order = $tempOrder;

            $this->save();
            $nextSlider->save();
        }
    }

    public function moveToPosition($newPosition)
    {
        $maxPosition = static::max('sort_order');
        $newPosition = max(1, min($newPosition, $maxPosition));

        if ($newPosition != $this->sort_order) {
            if ($newPosition > $this->sort_order) {
                // Moving down - shift others up
                static::whereBetween('sort_order', [$this->sort_order + 1, $newPosition])
                      ->decrement('sort_order');
            } else {
                // Moving up - shift others down
                static::whereBetween('sort_order', [$newPosition, $this->sort_order - 1])
                      ->increment('sort_order');
            }

            $this->sort_order = $newPosition;
            $this->save();
        }
    }

    // Route key binding
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}