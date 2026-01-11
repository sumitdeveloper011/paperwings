@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-history"></i>
                    Activity Log
                </h1>
                <p class="page-header__subtitle">Track all admin actions and changes</p>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="modern-card" style="margin-bottom: 2rem;">
        <div class="modern-card__header">
            <h3 class="modern-card__title">
                <i class="fas fa-filter"></i>
                Filters
            </h3>
        </div>
        <div class="modern-card__body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label for="user_id" class="form-label-modern">User</label>
                            <select name="user_id" id="user_id" class="form-input-modern">
                                <option value="">All Users</option>
                                @foreach($adminUsers as $adminUser)
                                    <option value="{{ $adminUser->id }}" {{ request('user_id') == $adminUser->id ? 'selected' : '' }}>
                                        {{ $adminUser->name }} ({{ $adminUser->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label for="action" class="form-label-modern">Action</label>
                            <select name="action" id="action" class="form-input-modern">
                                <option value="">All Actions</option>
                                @foreach($actionTypes as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label for="date_from" class="form-label-modern">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-input-modern" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group-modern">
                            <label for="date_to" class="form-label-modern">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-input-modern" value="{{ request('date_to') }}">
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Log Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    Activity Logs
                </h3>
                <p class="modern-card__subtitle">{{ $activities->total() }} total activities</p>
            </div>
        </div>

        <div class="modern-card__body">
            @if($activities->count() > 0)
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Subject</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                            <tr>
                                <td>
                                    <div style="font-weight: 600;">{{ $activity->created_at->format('M d, Y') }}</div>
                                    <div style="font-size: 0.875rem; color: #666;">{{ $activity->created_at->format('h:i A') }}</div>
                                </td>
                                <td>
                                    @if($activity->causer)
                                        <div style="font-weight: 600;">{{ $activity->causer->name }}</div>
                                        <div style="font-size: 0.875rem; color: #666;">{{ $activity->causer->email }}</div>
                                        @if($activity->causer->roles->count() > 0)
                                            <div style="margin-top: 0.25rem;">
                                                @foreach($activity->causer->roles as $role)
                                                    <span class="badge bg-primary" style="font-size: 0.75rem;">{{ $role->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activity->description == 'created')
                                        <span class="badge bg-success">
                                            <i class="fas fa-plus"></i> Created
                                        </span>
                                    @elseif($activity->description == 'updated')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-edit"></i> Updated
                                        </span>
                                    @elseif($activity->description == 'deleted')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-trash"></i> Deleted
                                        </span>
                                    @else
                                        <span class="badge bg-info">
                                            <i class="fas fa-info"></i> {{ ucfirst($activity->description) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($activity->subject)
                                        @if($activity->subject_type == 'App\Models\User')
                                            <div style="font-weight: 600;">User: {{ $activity->subject->name }}</div>
                                            <div style="font-size: 0.875rem; color: #666;">{{ $activity->subject->email }}</div>
                                        @else
                                            <div>{{ class_basename($activity->subject_type) }}</div>
                                            <div style="font-size: 0.875rem; color: #666;">ID: {{ $activity->subject_id }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activity->properties && isset($activity->properties['changes']))
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="showChanges({{ $activity->id }})">
                                            <i class="fas fa-eye"></i> View Changes
                                        </button>
                                        <div id="changes-{{ $activity->id }}" style="display: none; margin-top: 0.5rem; padding: 0.5rem; background: #f8f9fa; border-radius: 4px;">
                                            @foreach($activity->properties['changes'] as $field => $change)
                                                <div style="margin-bottom: 0.25rem;">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong><br>
                                                    <span style="color: #dc3545;">{{ is_array($change['old'] ?? null) ? implode(', ', $change['old']) : ($change['old'] ?? 'N/A') }}</span>
                                                    <i class="fas fa-arrow-right" style="margin: 0 0.5rem; color: #666;"></i>
                                                    <span style="color: #28a745;">{{ is_array($change['new'] ?? null) ? implode(', ', $change['new']) : ($change['new'] ?? 'N/A') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($activity->properties)
                                        <small class="text-muted">See properties</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper" style="margin-top: 2rem;">
                    {{ $activities->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-history fa-3x"></i>
                    <h3>No activity logs found</h3>
                    <p>There are no activity logs matching your criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function showChanges(activityId) {
    const changesDiv = document.getElementById('changes-' + activityId);
    if (changesDiv.style.display === 'none') {
        changesDiv.style.display = 'block';
    } else {
        changesDiv.style.display = 'none';
    }
}
</script>
@endsection
