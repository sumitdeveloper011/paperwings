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
                           placeholder="Enter full name"
                           required>
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
                            name="rating"
                            required>
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
                      data-required="true"
                      required>{{ old('review', $testimonial->review ?? '') }}</textarea>
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
        <div class="mb-3">
            <label for="image" class="form-label">Profile Image</label>

            <!-- Current Image Preview (Edit Page) -->
            @if($testimonial)
            <div class="mb-3">
                <label class="form-label">Current Image</label>
                <div class="image-preview-wrapper">
                    <div class="image-preview" id="currentImagePreview">
                        <img src="{{ $testimonial->image ? asset('storage/' . $testimonial->image) : asset('assets/images/profile.png') }}" 
                             alt="{{ $testimonial->name }}" 
                             class="image-preview__img"
                             onerror="this.src='{{ asset('assets/images/profile.png') }}'">
                        @if($testimonial->image)
                        <button type="button" class="image-preview__remove" onclick="removeCurrentImage()" title="Remove current image">
                            <i class="fas fa-times"></i>
                        </button>
                        @endif
                    </div>
                </div>
                <small class="form-text text-muted">
                    <i class="fas fa-info-circle"></i>
                    @if($testimonial->image)
                        Current image will be replaced if you upload a new one
                    @else
                        No image uploaded. Default profile image will be used.
                    @endif
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

            <!-- File Input -->
            <input type="file"
                   class="form-control @error('image') is-invalid @enderror"
                   id="image"
                   name="image"
                   accept="image/*">
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i>
                Recommended size: 300x300px. Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB
            </small>
            @error('image')
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
                            name="status"
                            required>
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

<style>
.image-preview-wrapper {
    margin-bottom: 1rem;
}

.image-preview {
    position: relative;
    display: inline-block;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid var(--border-color);
    width: 150px;
    height: 150px;
}

.image-preview__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
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
                    previewImg.src = e.target.result;
                    newImagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                newImagePreview.style.display = 'none';
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
        // Add hidden input to indicate image removal
        const form = document.querySelector('form');
        if (form && !form.querySelector('input[name="remove_image"]')) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_image';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
        }
    }
}
</script>
