<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\HasUuid;

class Role extends SpatieRole
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
