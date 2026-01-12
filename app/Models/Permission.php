<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\HasUuid;

class Permission extends SpatiePermission
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'guard_name',
    ];


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
