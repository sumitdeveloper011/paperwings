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
        'status' => 'integer'
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

    // Scope to filter active sliders
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // Scope to filter inactive sliders
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    // Scope to order sliders
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // Get status badge attribute
    public function getStatusBadgeAttribute()
    {
        return $this->status == 1 
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }

    // Get image URL attribute
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('assets/images/no-image.png');
    }

    // Get button count attribute
    public function getButtonCountAttribute()
    {
        return $this->buttons ? count($this->buttons) : 0;
    }

    // Get first button attribute
    public function getFirstButtonAttribute()
    {
        return $this->buttons && count($this->buttons) > 0 ? $this->buttons[0] : null;
    }

    // Get second button attribute
    public function getSecondButtonAttribute()
    {
        return $this->buttons && count($this->buttons) > 1 ? $this->buttons[1] : null;
    }

    // Get has buttons attribute
    public function getHasButtonsAttribute()
    {
        return $this->buttons && count($this->buttons) > 0;
    }

    // Move slider up in order
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

    // Move slider down in order
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

    // Move slider to specific position
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

    // Get route key name
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}