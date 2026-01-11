@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-eye"></i>
                    View Role
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Role Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-user-tag"></i>
                                Role Name
                            </div>
                            <div class="detail-item__value">
                                <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">{{ $role->name }}</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-shield-alt"></i>
                                Guard Name
                            </div>
                            <div class="detail-item__value">
                                <span class="badge bg-secondary">{{ $role->guard_name }}</span>
                            </div>
                        </div>

                        <div class="detail-item detail-item--full">
                            <div class="detail-item__label">
                                <i class="fas fa-key"></i>
                                Permissions ({{ $role->permissions->count() }})
                            </div>
                            <div class="detail-item__value">
                                @if($role->permissions->count() > 0)
                                    <div class="permissions-list">
                                        @foreach($permissions as $module => $modulePermissions)
                                            @php
                                                $rolePermissions = $role->permissions->whereIn('id', $modulePermissions->pluck('id'));
                                            @endphp
                                            @if($rolePermissions->count() > 0)
                                                <div class="permission-module mb-3" style="padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #667eea;">
                                                    <strong style="color: #495057; display: block; margin-bottom: 0.75rem; font-size: 1rem;">
                                                        <i class="fas fa-folder"></i> {{ ucfirst($module) }}
                                                    </strong>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                                        @foreach($rolePermissions as $permission)
                                                            <span class="badge bg-info" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">{{ $permission->name }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        No permissions assigned to this role.
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="detail-item detail-item--full">
                            <div class="detail-item__label">
                                <i class="fas fa-users"></i>
                                Assigned Users ({{ $role->users->count() }})
                            </div>
                            <div class="detail-item__value">
                                @if($role->users->count() > 0)
                                    <div class="users-list" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($role->users as $user)
                                            <div style="padding: 0.75rem; background: #f8f9fa; border-radius: 6px; display: flex; align-items: center; gap: 0.75rem;">
                                                <i class="fas fa-user-circle" style="color: #667eea; font-size: 1.25rem;"></i>
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        No users assigned to this role.
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-calendar-plus"></i>
                                Created At
                            </div>
                            <div class="detail-item__value">
                                {{ $role->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-calendar-edit"></i>
                                Updated At
                            </div>
                            <div class="detail-item__value">
                                {{ $role->updated_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

