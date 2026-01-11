<?php

namespace App\Http\Controllers\Admin\ActivityLog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    // Display activity logs
    public function index(Request $request): View
    {
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Filter by user (who performed the action)
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)
                  ->where('causer_type', User::class);
        }

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('description', $request->action);
        }

        // Filter by model/entity
        if ($request->filled('model_type')) {
            $query->where('subject_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->paginate(50);

        // Get all admin users for filter
        $adminUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['SuperAdmin', 'Admin', 'Manager', 'Editor']);
        })->orderBy('first_name')->orderBy('last_name')->get();

        // Get unique action types
        $actionTypes = Activity::distinct()->pluck('description')->sort()->values();

        // Get unique model types
        $modelTypes = Activity::distinct()->pluck('subject_type')->filter()->sort()->values();

        return view('admin.activity-log.index', compact(
            'activities',
            'adminUsers',
            'actionTypes',
            'modelTypes'
        ));
    }
}
