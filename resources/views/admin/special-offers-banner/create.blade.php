@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Special Offers Banner
                </h1>
                <p class="page-header__subtitle">Create a new promotional banner</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.special-offers-banners.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Banners</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.special-offers-banners.store') }}" class="modern-form" id="bannerForm" enctype="multipart/form-data" novalidate>
                @csrf

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
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   placeholder="Enter banner title"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Enter banner description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-image"></i>
                            Banner Image
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Recommended size: 1920x600px. Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB
                            </small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Button Settings -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-mouse-pointer"></i>
                            Button Settings
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="button_text" class="form-label">Button Text</label>
                                    <input type="text"
                                           class="form-control @error('button_text') is-invalid @enderror"
                                           id="button_text"
                                           name="button_text"
                                           value="{{ old('button_text') }}"
                                           placeholder="e.g., Shop Now">
                                    @error('button_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    @include('components.smart-link-selector', [
                                        'name' => 'button_link',
                                        'id' => 'button_link',
                                        'label' => 'Button Link',
                                        'value' => old('button_link'),
                                        'required' => false,
                                        'categories' => $categories ?? collect(),
                                        'bundles' => $bundles ?? collect(),
                                        'pages' => $pages ?? collect(),
                                    ])
                                    @error('button_link')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validity Period -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-calendar-alt"></i>
                            Validity Period
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="datetime-local"
                                           class="form-control @error('start_date') is-invalid @enderror"
                                           id="start_date"
                                           name="start_date"
                                           value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="datetime-local"
                                           class="form-control @error('end_date') is-invalid @enderror"
                                           id="end_date"
                                           name="end_date"
                                           value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input @error('show_countdown') is-invalid @enderror"
                                       id="show_countdown"
                                       name="show_countdown"
                                       value="1"
                                       {{ old('show_countdown') ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_countdown">
                                    Show Countdown Timer
                                </label>
                            </div>
                            @error('show_countdown')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-cog"></i>
                            Additional Settings
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number"
                                           class="form-control @error('sort_order') is-invalid @enderror"
                                           id="sort_order"
                                           name="sort_order"
                                           value="{{ old('sort_order', 0) }}"
                                           min="0"
                                           placeholder="0">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Lower numbers appear first. Leave 0 for auto-ordering.
                                    </small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status"
                                            required>
                                        <option value="">Select Status</option>
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Create Banner
                    </button>
                    <a href="{{ route('admin.special-offers-banners.index') }}" class="btn btn-outline-secondary btn-lg" style="background-color: #f8f9fa;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.special-offers-banner.partials.tips')
        </div>
    </div>
</div>
@endsection
