@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Special Offers Banner
                </h1>
                <p class="page-header__subtitle">Update banner information</p>
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
            <form method="POST" action="{{ route('admin.special-offers-banners.update', $specialOffersBanner) }}" class="modern-form" id="bannerForm" enctype="multipart/form-data" novalidate>
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
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $specialOffersBanner->title) }}"
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
                                      placeholder="Enter banner description">{{ old('description', $specialOffersBanner->description) }}</textarea>
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
                        @if($specialOffersBanner->image_url)
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div class="image-preview-wrapper">
                                <div class="image-preview" id="currentImagePreview">
                                    <img src="{{ $specialOffersBanner->image_url }}" alt="{{ $specialOffersBanner->title }}" class="image-preview__img">
                                    <button type="button" class="image-preview__remove" onclick="removeCurrentImage()" title="Remove current image">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Current image will be replaced if you upload a new one
                            </small>
                        </div>
                        @endif

                        <!-- New Image Preview -->
                        <div class="image-preview-wrapper" id="newImagePreview" style="display: none;">
                            <div class="image-preview">
                                <img id="previewImg" src="" alt="Preview" class="image-preview__img">
                                <button type="button" class="image-preview__remove" onclick="removeImagePreview()" title="Remove preview">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Upload New Image</label>
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
                                           value="{{ old('button_text', $specialOffersBanner->button_text) }}"
                                           placeholder="e.g., Shop Now">
                                    @error('button_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="button_link" class="form-label">Button Link</label>
                                    <input type="url"
                                           class="form-control @error('button_link') is-invalid @enderror"
                                           id="button_link"
                                           name="button_link"
                                           value="{{ old('button_link', $specialOffersBanner->button_link) }}"
                                           placeholder="https://example.com">
                                    @error('button_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
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
                                           value="{{ old('start_date', $specialOffersBanner->start_date ? $specialOffersBanner->start_date->format('Y-m-d\TH:i') : '') }}">
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
                                           value="{{ old('end_date', $specialOffersBanner->end_date ? $specialOffersBanner->end_date->format('Y-m-d\TH:i') : '') }}">
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
                                       {{ old('show_countdown', $specialOffersBanner->show_countdown) ? 'checked' : '' }}>
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
                                           value="{{ old('sort_order', $specialOffersBanner->sort_order ?? 0) }}"
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
                                        <option value="1" {{ old('status', $specialOffersBanner->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $specialOffersBanner->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden input for image removal -->
                <input type="hidden" name="remove_image" id="remove_image" value="0">

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Update Banner
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

<style>
.image-preview-wrapper {
    margin-bottom: 1rem;
}

.image-preview {
    position: relative;
    display: inline-block;
    border-radius: 8px;
    overflow: hidden;
    border: 3px solid var(--border-color);
    max-width: 100%;
}

.image-preview__img {
    width: 100%;
    height: auto;
    display: block;
    max-width: 400px;
}

.image-preview__remove {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-preview__remove:hover {
    background: rgba(220, 53, 69, 1);
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const newImagePreview = document.getElementById('newImagePreview');
    const previewImg = document.getElementById('previewImg');

    // Image preview functionality
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImg) {
                        previewImg.src = e.target.result;
                        if (newImagePreview) {
                            newImagePreview.style.display = 'block';
                        }
                    }
                };
                reader.readAsDataURL(file);
            } else {
                if (newImagePreview) {
                    newImagePreview.style.display = 'none';
                }
            }
        });
    }
});

// Remove new image preview
function removeImagePreview() {
    const imageInput = document.getElementById('image');
    const newImagePreview = document.getElementById('newImagePreview');
    if (imageInput) {
        imageInput.value = '';
    }
    if (newImagePreview) {
        newImagePreview.style.display = 'none';
    }
}

// Remove current image (for edit page)
function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current image? You will need to upload a new one.')) {
        const currentPreview = document.getElementById('currentImagePreview');
        if (currentPreview) {
            currentPreview.style.display = 'none';
        }
        // Set hidden input to indicate image removal
        const removeImageInput = document.getElementById('remove_image');
        if (removeImageInput) {
            removeImageInput.value = '1';
        }
    }
}
</script>
@endsection
