<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    /**
     * Boot the trait
     */
    protected static function bootHasUniqueSlug()
    {
        static::creating(function ($model) {
            if (empty($model->slug) && isset($model->name)) {
                $model->slug = static::makeUniqueSlug($model->name);
            } elseif (empty($model->slug) && isset($model->title)) {
                $model->slug = static::makeUniqueSlug($model->title);
            }
        });

        static::updating(function ($model) {
            $nameField = isset($model->name) ? 'name' : (isset($model->title) ? 'title' : null);

            if ($nameField && $model->isDirty($nameField) && !$model->isDirty('slug')) {
                $model->slug = static::makeUniqueSlug($model->$nameField, $model->id);
            }
        });
    }

    /**
     * Generate a unique slug
     *
     * @param string $name
     * @param int|null $excludeId
     * @return string
     */
    protected static function makeUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
