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
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Role Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="modern-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">Role Name <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user-tag input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $role->name) }}"
                                       placeholder="e.g., Manager, Editor"
                                       required>
                            </div>
                            <small class="form-text text-muted">Role name will be converted to lowercase with underscores</small>
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="guard_name" class="form-label-modern">Guard Name</label>
                            <div class="input-wrapper">
                                <i class="fas fa-shield-alt input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('guard_name') is-invalid @enderror"
                                       id="guard_name"
                                       name="guard_name"
                                       value="{{ old('guard_name', $role->guard_name) }}"
                                       placeholder="web">
                            </div>
                            @error('guard_name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label-modern">Permissions</label>
                            <div class="permissions-wrapper" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 1rem;">
                                @foreach($permissions as $module => $modulePermissions)
                                    <div class="permission-module mb-3">
                                        <h5 class="mb-2" style="font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6; padding-bottom: 0.5rem;">
                                            <i class="fas fa-folder"></i> {{ ucfirst($module) }}
                                        </h5>
                                        <div class="row">
                                            @foreach($modulePermissions as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check-modern">
                                                        <input type="checkbox"
                                                               class="form-check-input-modern"
                                                               id="permission_{{ $permission->id }}"
                                                               name="permissions[]"
                                                               value="{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                        <label class="form-check-label-modern" for="permission_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Role
                            </button>
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

