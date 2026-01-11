<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Support\Str;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'uuid',
        'name',
        'guard_name',
    ];

    /**
     * Boot method to generate UUID automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permission) {
            if (empty($permission->uuid)) {
                $permission->uuid = Str::uuid();
            }
        });
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
