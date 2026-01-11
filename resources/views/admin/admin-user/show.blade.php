@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-user-shield"></i>
                    {{ $user->name }}
                </h1>
                <p class="page-header__subtitle">Admin user details and activity</p>
            </div>
            <div class="page-header__actions">
                @if(!$user->hasRole('SuperAdmin') || Auth::user()->hasRole('SuperAdmin'))
                <a href="{{ route('admin.admin-users.edit', $user) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit User</span>
                </a>
                @endif
                <a href="{{ route('admin.admin-users.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Profile Card -->
        <div class="col-lg-4">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-user-circle"></i>
                        Admin User Profile
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="user-profile-header">
                        <div class="user-profile-avatar">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="user-profile-avatar__img">
                            @else
                                <div class="user-profile-avatar__placeholder">
                                    {{ strtoupper(substr($user->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="user-profile-name">{{ $user->name }}</h3>
                        <p class="user-profile-email">
                            <i class="fas fa-envelope"></i>
                            {{ $user->email }}
                        </p>
                        <div class="user-profile-status">
                            <span class="badge badge-{{ $user->status == 1 ? 'success' : 'danger' }}">
                                {{ $user->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                            @if($user->hasVerifiedEmail())
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                            @endif
                        </div>
                        @if($user->roles && $user->roles->count() > 0)
                        <div class="user-profile-roles" style="margin-top: 1rem;">
                            <strong style="display: block; margin-bottom: 0.5rem; color: #2c3e50;">Roles:</strong>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: center;">
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-user-tag"></i> {{ $role->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <div class="user-info-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e9ecef;">
                        <div class="user-info-item" style="margin-bottom: 1rem;">
                            <strong style="color: #666; display: block; margin-bottom: 0.25rem;">Phone:</strong>
                            <span>{{ $user->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="user-info-item" style="margin-bottom: 1rem;">
                            <strong style="color: #666; display: block; margin-bottom: 0.25rem;">Created:</strong>
                            <span>{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="user-info-item">
                            <strong style="color: #666; display: block; margin-bottom: 0.25rem;">Last Updated:</strong>
                            <span>{{ $user->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-history"></i>
                        Activity Log
                    </h3>
                </div>
                <div class="modern-card__body">
                    @if($activities->count() > 0)
                        <div class="activity-list">
                            @foreach($activities as $activity)
                            <div class="activity-item" style="padding: 1rem; border-bottom: 1px solid #e9ecef; display: flex; gap: 1rem;">
                                <div class="activity-icon" style="flex-shrink: 0;">
                                    @if($activity->description == 'created')
                                        <i class="fas fa-plus-circle" style="color: #28a745; font-size: 1.5rem;"></i>
                                    @elseif($activity->description == 'updated')
                                        <i class="fas fa-edit" style="color: #ffc107; font-size: 1.5rem;"></i>
                                    @elseif($activity->description == 'deleted')
                                        <i class="fas fa-trash" style="color: #dc3545; font-size: 1.5rem;"></i>
                                    @else
                                        <i class="fas fa-info-circle" style="color: #667eea; font-size: 1.5rem;"></i>
                                    @endif
                                </div>
                                <div class="activity-content" style="flex: 1;">
                                    <div class="activity-description" style="font-weight: 600; margin-bottom: 0.25rem;">
                                        {{ ucfirst($activity->description) }} admin user
                                    </div>
                                    @if($activity->causer)
                                    <div class="activity-causer" style="color: #666; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                        By: {{ $activity->causer->name }} ({{ $activity->causer->email }})
                                    </div>
                                    @endif
                                    @if($activity->properties && isset($activity->properties['changes']))
                                    <div class="activity-changes" style="font-size: 0.875rem; color: #666;">
                                        <strong>Changes:</strong>
                                        <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                                            @foreach($activity->properties['changes'] as $field => $change)
                                            <li>
                                                <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                                                {{ is_array($change['old'] ?? null) ? implode(', ', $change['old']) : ($change['old'] ?? 'N/A') }}
                                                → 
                                                {{ is_array($change['new'] ?? null) ? implode(', ', $change['new']) : ($change['new'] ?? 'N/A') }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    <div class="activity-time" style="color: #999; font-size: 0.75rem; margin-top: 0.5rem;">
                                        {{ $activity->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-history fa-3x"></i>
                            <h3>No activity found</h3>
                            <p>There is no activity log for this admin user yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
