@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Role
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.roles.update', $role->id) }}" class="modern-form">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $role->name) }}"
                                   placeholder="e.g., Manager, Editor"
                                   required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Role name will be converted to lowercase with underscores (e.g., "Manager" becomes "manager").
                            </small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="guard_name" class="form-label">Guard Name</label>
                            <input type="text"
                                   class="form-control @error('guard_name') is-invalid @enderror"
                                   id="guard_name"
                                   name="guard_name"
                                   value="{{ old('guard_name', $role->guard_name) }}"
                                   placeholder="web">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Determines which authentication guard the role belongs to. Default: web.
                            </small>
                            @error('guard_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-key"></i>
                            Permissions
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <label class="form-label">Select Permissions</label>
                            <div class="permissions-wrapper" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 1rem;">
                                @foreach($permissions as $module => $modulePermissions)
                                    <div class="permission-module mb-3">
                                        <h5 class="mb-2" style="font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 0.5rem;">
                                            <i class="fas fa-folder"></i> {{ ucfirst($module) }}
                                        </h5>
                                        <div class="row">
                                            @foreach($modulePermissions as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               id="permission_{{ $permission->id }}"
                                                               name="permissions[]"
                                                               value="{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Select the permissions you want to assign to this role. Permissions are grouped by module for easier selection.
                            </small>
                            @error('permissions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Update Role
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.role.partials.tips')
        </div>
    </div>
</div>
@endsection

