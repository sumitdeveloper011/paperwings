@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-eye"></i>
                    View Permission
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">Permission Details</h3>
                </div>
                <div class="modern-card__body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">{{ $permission->name }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Module:</strong>
                        </div>
                        <div class="col-md-8">
                            @php
                                $parts = explode('.', $permission->name);
                                $module = $parts[0] ?? 'other';
                            @endphp
                            <span class="badge bg-secondary">{{ ucfirst($module) }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Guard:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-info">{{ $permission->guard_name }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Roles ({{ $permission->roles->count() }}):</strong>
                        </div>
                        <div class="col-md-8">
                            @if($permission->roles->count() > 0)
                                <div>
                                    @foreach($permission->roles as $role)
                                        <span class="badge bg-primary me-1 mb-1">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No roles assigned</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Created At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $permission->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Updated At:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $permission->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

