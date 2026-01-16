<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasUuid;
use App\Traits\HasUniqueSlug;

class EmailTemplate extends Model
{
    use HasFactory, HasUuid, HasUniqueSlug;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'subject',
        'body',
        'variables',
        'description',
        'category',
        'is_active',
        'version',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'version' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = static::makeUniqueSlug($template->name);
            }
        });

        static::updating(function ($template) {
            if ($template->isDirty('name') && !$template->isDirty('slug')) {
                $template->slug = static::makeUniqueSlug($template->name, $template->id);
            }
        });
    }

    public function variables()
    {
        return $this->hasMany(EmailTemplateVariable::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
