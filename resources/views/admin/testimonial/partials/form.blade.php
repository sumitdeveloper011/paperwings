@php
    $testimonial = $testimonial ?? null;
@endphp

<!-- Basic Information -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-info-circle"></i>
            Basic Information
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name', $testimonial->name ?? '') }}"
                           placeholder="Enter full name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email', $testimonial->email ?? '') }}"
                           placeholder="Enter email address">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="designation" class="form-label">Designation</label>
                    <input type="text"
                           class="form-control @error('designation') is-invalid @enderror"
                           id="designation"
                           name="designation"
                           value="{{ old('designation', $testimonial->designation ?? '') }}"
                           placeholder="e.g., CEO, Manager">
                    @error('designation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                    <select class="form-select @error('rating') is-invalid @enderror"
                            id="rating"
                            name="rating">
                        <option value="">Select Rating</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? '') == $i ? 'selected' : '' }}>
                                {{ $i }} Star{{ $i > 1 ? 's' : '' }}
                            </option>
                        @endfor
                    </select>
                    @error('rating')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-comment"></i>
            Review
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="mb-3">
            <label for="review" class="form-label">Review <span class="text-danger">*</span></label>
            <textarea class="form-control @error('review') is-invalid @enderror"
                      id="review"
                      name="review"
                      rows="6"
                      placeholder="Enter customer review or testimonial"
                      data-required="true">{{ old('review', $testimonial->review ?? '') }}</textarea>
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i>
                Write a detailed review or testimonial from the customer
            </small>
            @error('review')
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
            Profile Image
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="form-group-modern">
            <label for="image" class="form-label-modern">Profile Image</label>
            <x-image-requirements type="testimonial" />
            
            <!-- Current Image Preview (Edit Page) -->
            @if($testimonial && $testimonial->image_url)
            <div class="form-group-modern mb-3">
                <label class="form-label-modern">Current Image</label>
                <div class="image-preview">
                    <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}" class="image-preview__img">
                </div>
                <div class="form-hint">
                    <i class="fas fa-info-circle"></i>
                    Current image will be replaced if you upload a new one
                </div>
            </div>
            @endif

            <div class="file-upload-wrapper">
                <input type="file"
                       class="file-upload-input @error('image') is-invalid @enderror"
                       id="image"
                       name="image"
                       accept="image/*">
                <label for="image" class="file-upload-label">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Choose {{ $testimonial && $testimonial->image_url ? 'New ' : '' }}Image</span>
                </label>
            </div>
            @error('image')
                <div class="form-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group-modern" id="imagePreview" style="display: none;">
            <label class="form-label-modern">New Image Preview</label>
            <div class="image-preview">
                <img id="previewImg" src="" alt="Preview" class="image-preview__img">
                <button type="button" class="image-preview__remove" onclick="removeImagePreview()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
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
                           value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}"
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
                            name="status">
                        <option value="">Select Status</option>
                        <option value="1" {{ old('status', $testimonial->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $testimonial->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    // Image preview functionality
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }
});

// Remove new image preview
function removeImagePreview() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    if (imageInput) {
        imageInput.value = '';
    }
    if (imagePreview) {
        imagePreview.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('testimonialForm');
    if (form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('invalid', function(ev) {
                ev.preventDefault();
                ev.stopPropagation();
            }, true);
        });
    }
});
</script>
