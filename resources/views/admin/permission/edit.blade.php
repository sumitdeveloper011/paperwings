@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Permission
                </h1>
            </div>
            <div class="page-header__actions">
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
                    <h3 class="modern-card__title">Permission Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.permissions.update', $permission) }}" class="modern-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">Permission Name <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $permission->name) }}"
                                       placeholder="e.g., products.create, users.edit"
                                       required>
                            </div>
                            <small class="form-text text-muted">Use dot notation: module.action (e.g., products.create, users.edit)</small>
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
                                       value="{{ old('guard_name', $permission->guard_name) }}"
                                       placeholder="web">
                            </div>
                            <small class="form-text text-muted">Default: web</small>
                            @error('guard_name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Permission
                            </button>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if($permission->roles->count() > 0)
                <div class="modern-card modern-card--compact mt-3">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-users"></i>
                            Assigned Roles ({{ $permission->roles->count() }})
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($permission->roles as $role)
                                <a href="{{ route('admin.roles.show', $role) }}" class="badge bg-primary" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                    {{ $role->name }}
                                </a>
                            @endforeach
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            <small>This permission is currently assigned to {{ $permission->roles->count() }} role(s).
                            Changing the permission name may affect access control.</small>
                        </p>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-lightbulb"></i>
                        Tips & Guidelines
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-box">
                        <h4 class="info-box__title">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Important Notes
                        </h4>
                        <ul class="info-box__list">
                            <li>Changing permission name may break existing access controls</li>
                            <li>Ensure all roles are updated if you change the name</li>
                            <li>Check middleware and route permissions after changes</li>
                        </ul>
                    </div>

                    <div class="info-box mt-3">
                        <h4 class="info-box__title">
                            <i class="fas fa-info-circle text-info"></i>
                            Permission Naming
                        </h4>
                        <p class="info-box__text">
                            Follow the dot notation pattern:
                        </p>
                        <ul class="info-box__list">
                            <li><code>module.view</code></li>
                            <li><code>module.create</code></li>
                            <li><code>module.edit</code></li>
                            <li><code>module.delete</code></li>
                        </ul>
                    </div>

                    <div class="info-box mt-3">
                        <h4 class="info-box__title">
                            <i class="fas fa-shield-alt text-success"></i>
                            Guard Name
                        </h4>
                        <p class="info-box__text">
                            The guard determines which authentication system this permission applies to.
                            Keep it as <code>web</code> unless you're using multiple guards.
                        </p>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This permission is assigned to {{ $permission->roles->count() }} role(s).
                        Make sure to review role assignments after editing.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

