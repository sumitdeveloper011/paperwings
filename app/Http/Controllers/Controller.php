<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

abstract class Controller
{
    /**
     * Check if user has permission, redirect if not
     */
    protected function checkPermission(string $permission, string $redirectRoute = 'admin.dashboard'): ?RedirectResponse
    {
        if (!Auth::user()->can($permission)) {
            return redirect()->route($redirectRoute)
                ->with('error', 'You do not have permission to perform this action.');
        }
        return null;
    }
}
