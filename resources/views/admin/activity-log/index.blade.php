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
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="activity-filter-form">
                <div class="row g-3">
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
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Date & Time</th>
                                <th class="modern-table__th">User</th>
                                <th class="modern-table__th">Action</th>
                                <th class="modern-table__th">Subject</th>
                                <th class="modern-table__th">Details</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($activities as $activity)
                            <tr class="modern-table__row">
                                <td class="modern-table__td">
                                    <div style="font-weight: 600;">{{ $activity->created_at->format('M d, Y') }}</div>
                                    <div style="font-size: 0.875rem; color: #666;">{{ $activity->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="modern-table__td">
                                    @if($activity->causer)
                                        <div style="font-weight: 600;">{{ $activity->causer->name ?? 'N/A' }}</div>
                                        <div style="font-size: 0.875rem; color: #666;">{{ $activity->causer->email ?? 'N/A' }}</div>
                                        @if($activity->causer->roles && $activity->causer->roles->count() > 0)
                                            <div style="margin-top: 0.25rem;">
                                                @foreach($activity->causer->roles as $role)
                                                    <span class="badge bg-primary" style="font-size: 0.75rem;">{{ $role->name ?? 'N/A' }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td class="modern-table__td">
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
                                <td class="modern-table__td">
                                    @if($activity->subject)
                                        @if($activity->subject_type == 'App\Models\User')
                                            <div style="font-weight: 600;">User: {{ $activity->subject->name ?? 'N/A' }}</div>
                                            <div style="font-size: 0.875rem; color: #666;">{{ $activity->subject->email ?? 'N/A' }}</div>
                                        @else
                                            <div>{{ class_basename($activity->subject_type ?? 'Unknown') }}</div>
                                            <div style="font-size: 0.875rem; color: #666;">ID: {{ $activity->subject_id ?? 'N/A' }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="modern-table__td">
                                    @php
                                        $properties = is_string($activity->properties) ? json_decode($activity->properties, true) : $activity->properties;
                                    @endphp
                                    @if($properties && isset($properties['changes']) && is_array($properties['changes']))
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="showChanges({{ $activity->id }})">
                                            <i class="fas fa-eye"></i> View Changes
                                        </button>
                                        <div id="changes-{{ $activity->id }}" class="properties-display" style="display: none; margin-top: 0.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                                            @foreach($properties['changes'] as $field => $change)
                                                @if(is_array($change))
                                                    <div style="margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e9ecef;">
                                                        <strong style="color: #495057;">{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong><br>
                                                        <div style="margin-top: 0.25rem;">
                                                            <span style="color: #dc3545; background: #fff5f5; padding: 0.25rem 0.5rem; border-radius: 3px; display: inline-block; margin-right: 0.5rem;">
                                                                {{ is_array($change['old'] ?? null) ? json_encode($change['old'], JSON_UNESCAPED_UNICODE) : ($change['old'] ?? 'N/A') }}
                                                            </span>
                                                            <i class="fas fa-arrow-right" style="margin: 0 0.5rem; color: #6c757d;"></i>
                                                            <span style="color: #28a745; background: #f0fff4; padding: 0.25rem 0.5rem; border-radius: 3px; display: inline-block;">
                                                                {{ is_array($change['new'] ?? null) ? json_encode($change['new'], JSON_UNESCAPED_UNICODE) : ($change['new'] ?? 'N/A') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @elseif($properties && !empty($properties))
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="showProperties({{ $activity->id }})">
                                            <i class="fas fa-info-circle"></i> See Properties
                                        </button>
                                        <div id="properties-{{ $activity->id }}" class="properties-display" style="display: none; margin-top: 0.5rem; padding: 0.75rem; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; max-width: 400px;">
                                            <pre style="margin: 0; font-size: 0.875rem; color: #495057; white-space: pre-wrap; word-wrap: break-word;">{{ json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
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
                @if($activities->hasPages())
                    <div class="pagination-wrapper" style="margin-top: 2rem;">
                        {{ $activities->appends(request()->query())->links('components.pagination') }}
                    </div>
                @endif
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

@push('styles')
<style>
/* Filter Form Styling */
.activity-filter-form {
    width: 100%;
}

.activity-filter-form .row {
    margin-bottom: 0;
}

.activity-filter-form .form-group-modern {
    margin-bottom: 0;
}

.activity-filter-form .form-actions {
    display: flex;
    flex-direction: row;
    gap: 0.75rem;
    align-items: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color, #e5e7eb);
}

.activity-filter-form .form-actions .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.activity-filter-form .form-actions .btn-primary {
    background: var(--primary-color, #667eea);
    color: white;
}

.activity-filter-form .form-actions .btn-primary:hover {
    background: #5568d3;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.activity-filter-form .form-actions .btn-outline-secondary {
    background: transparent;
    color: #6c757d;
    border: 1px solid #6c757d;
}

.activity-filter-form .form-actions .btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

/* Properties Display */
.properties-display {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Table Button Styling */
.modern-table__td {
    vertical-align: top;
    padding: 1rem;
}

.modern-table__td .btn {
    margin: 0;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.modern-table__td .btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 4px;
    border: 1px solid;
    transition: all 0.2s ease;
    cursor: pointer;
}

.modern-table__td .btn-outline-info {
    color: #0dcaf0;
    border-color: #0dcaf0;
    background-color: transparent;
}

.modern-table__td .btn-outline-info:hover {
    color: #000;
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.modern-table__td .btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
    background-color: transparent;
}

.modern-table__td .btn-outline-secondary:hover {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
    .activity-filter-form .row {
        margin-bottom: 1rem;
    }

    .activity-filter-form .form-actions {
        flex-direction: column;
        width: 100%;
    }

    .activity-filter-form .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
function showChanges(activityId) {
    const changesDiv = document.getElementById('changes-' + activityId);
    if (changesDiv) {
        if (changesDiv.style.display === 'none' || changesDiv.style.display === '') {
            changesDiv.style.display = 'block';
        } else {
            changesDiv.style.display = 'none';
        }
    }
}

function showProperties(activityId) {
    const propertiesDiv = document.getElementById('properties-' + activityId);
    if (propertiesDiv) {
        if (propertiesDiv.style.display === 'none' || propertiesDiv.style.display === '') {
            propertiesDiv.style.display = 'block';
        } else {
            propertiesDiv.style.display = 'none';
        }
    }
}
</script>
@endpush
@endsection
