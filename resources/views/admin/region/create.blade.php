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
                        
                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">
                                Region Name <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g., New York"
                                       required>
                            </div>
                            @error('name')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="slug" class="form-label-modern">
                                Slug
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-link input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('slug') is-invalid @enderror" 
                                       id="slug" 
                                       name="slug" 
                                       value="{{ old('slug') }}" 
                                       placeholder="e.g., new-york">
                            </div>
                            @error('slug')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text">Leave empty to auto-generate from name</small>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-cog"></i>
                            Settings
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="form-group-modern">
                            <div class="checkbox-modern">
                                <input type="checkbox" 
                                       id="status" 
                                       name="status" 
                                       value="1"
                                       {{ old('status', 1) ? 'checked' : '' }}>
                                <label for="status" class="checkbox-modern__label">
                                    <span class="checkbox-modern__check"></span>
                                    <span class="checkbox-modern__text">Active</span>
                                </label>
                            </div>
                            <small class="form-text">Enable or disable this region</small>
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

