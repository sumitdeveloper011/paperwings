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
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.special-offers-banners.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">Banner Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.special-offers-banners.store') }}" class="modern-form" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group-modern">
                            <label for="title" class="form-label-modern">Title <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}" 
                                       placeholder="Enter banner title"
                                       required>
                            </div>
                            @error('title')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="description" class="form-label-modern">Description</label>
                            <textarea class="form-input-modern @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Enter banner description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">Image</label>
                            <input type="file" 
                                   class="form-input-modern @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                            @error('image')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="button_text" class="form-label-modern">Button Text</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-mouse-pointer input-icon"></i>
                                        <input type="text" 
                                               class="form-input-modern @error('button_text') is-invalid @enderror" 
                                               id="button_text" 
                                               name="button_text" 
                                               value="{{ old('button_text') }}" 
                                               placeholder="e.g., Shop Now">
                                    </div>
                                    @error('button_text')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="button_link" class="form-label-modern">Button Link</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-link input-icon"></i>
                                        <input type="url" 
                                               class="form-input-modern @error('button_link') is-invalid @enderror" 
                                               id="button_link" 
                                               name="button_link" 
                                               value="{{ old('button_link') }}" 
                                               placeholder="https://example.com">
                                    </div>
                                    @error('button_link')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="start_date" class="form-label-modern">Start Date</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar input-icon"></i>
                                        <input type="datetime-local" 
                                               class="form-input-modern @error('start_date') is-invalid @enderror" 
                                               id="start_date" 
                                               name="start_date" 
                                               value="{{ old('start_date') }}">
                                    </div>
                                    @error('start_date')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="end_date" class="form-label-modern">End Date</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar input-icon"></i>
                                        <input type="datetime-local" 
                                               class="form-input-modern @error('end_date') is-invalid @enderror" 
                                               id="end_date" 
                                               name="end_date" 
                                               value="{{ old('end_date') }}">
                                    </div>
                                    @error('end_date')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group-modern">
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
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="sort_order" class="form-label-modern">Sort Order</label>
                            <div class="input-wrapper">
                                <i class="fas fa-sort input-icon"></i>
                                <input type="number" 
                                       class="form-input-modern @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="{{ old('sort_order', 0) }}" 
                                       min="0">
                            </div>
                            @error('sort_order')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="status" class="form-label-modern">Status <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select class="form-input-modern @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Create Banner
                            </button>
                            <a href="{{ route('admin.special-offers-banners.index') }}" class="btn btn-outline-secondary">
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

