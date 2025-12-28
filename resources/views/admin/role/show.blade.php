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
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Role Details</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">{{ $role->name }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Guard:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-secondary">{{ $role->guard_name }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Permissions ({{ $role->permissions->count() }}):</strong>
                        </div>
                        <div class="col-md-8">
                            @if($role->permissions->count() > 0)
                                <div class="permissions-list">
                                    @foreach($permissions as $module => $modulePermissions)
                                        @php
                                            $rolePermissions = $role->permissions->whereIn('id', $modulePermissions->pluck('id'));
                                        @endphp
                                        @if($rolePermissions->count() > 0)
                                            <div class="permission-module mb-2">
                                                <strong style="color: #495057;">{{ ucfirst($module) }}:</strong>
                                                <div class="mt-1">
                                                    @foreach($rolePermissions as $permission)
                                                        <span class="badge bg-info me-1 mb-1">{{ $permission->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No permissions assigned</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Users ({{ $role->users->count() }}):</strong>
                        </div>
                        <div class="col-md-8">
                            @if($role->users->count() > 0)
                                <ul class="list-unstyled">
                                    @foreach($role->users as $user)
                                        <li>
                                            <i class="fas fa-user"></i> {{ $user->name }} ({{ $user->email }})
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">No users assigned</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $role->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Updated At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $role->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

