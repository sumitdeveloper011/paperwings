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
                            <small class="form-text text-muted">Use dot notation: module.action</small>
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
        </div>
    </div>
</div>
@endsection

