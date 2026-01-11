@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Region
                </h1>
                <p class="page-header__subtitle">Create a new shipping region</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.regions.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Regions</span>
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.regions.store') }}" class="modern-form" id="regionForm">
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Region Information
                        </h3>
                    </div>
                    <div class="modern-card__body">

                        <div class="mb-3">
                            <label for="name" class="form-label">Region Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., New York"
                                   required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Enter a clear, descriptive region name.
                            </small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   id="slug"
                                   name="slug"
                                   value="{{ old('slug') }}"
                                   placeholder="e.g., new-york">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Leave empty to auto-generate from name. Slug is URL-friendly.
                            </small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                @include('admin.region.partials.tips')

                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-cog"></i>
                            Settings
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="status"
                                       name="status"
                                       value="1"
                                       {{ old('status', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Enable or disable this region for shipping calculations.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-save"></i>
                        Create Region
                    </button>
                    <a href="{{ route('admin.regions.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

