@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Permission
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
                    <form method="POST" action="{{ route('admin.permissions.store') }}" class="modern-form">
                        @csrf

                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">Permission Name <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
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
                                       value="{{ old('guard_name', 'web') }}"
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
                                Create Permission
                            </button>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
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
                            <i class="fas fa-info-circle text-info"></i>
                            Permission Naming Convention
                        </h4>
                        <p class="info-box__text">
                            Use dot notation to organize permissions by module and action:
                        </p>
                        <ul class="info-box__list">
                            <li><code>module.view</code> - View/list items</li>
                            <li><code>module.create</code> - Create new items</li>
                            <li><code>module.edit</code> - Edit existing items</li>
                            <li><code>module.delete</code> - Delete items</li>
                        </ul>
                    </div>

                    <div class="info-box mt-3">
                        <h4 class="info-box__title">
                            <i class="fas fa-examples text-success"></i>
                            Examples
                        </h4>
                        <ul class="info-box__list">
                            <li><code>products.view</code></li>
                            <li><code>products.create</code></li>
                            <li><code>users.edit</code></li>
                            <li><code>orders.delete</code></li>
                        </ul>
                    </div>

                    <div class="info-box mt-3">
                        <h4 class="info-box__title">
                            <i class="fas fa-shield-alt text-warning"></i>
                            Guard Name
                        </h4>
                        <p class="info-box__text">
                            The guard name determines which authentication guard this permission applies to.
                            For most admin panels, use <code>web</code>.
                        </p>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Pro Tip:</strong> After creating a permission, assign it to roles to control access.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

