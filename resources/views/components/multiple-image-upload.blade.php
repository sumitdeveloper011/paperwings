@props([
    'name' => 'images',
    'id' => 'images',
    'label' => 'Upload Images',
    'existingImages' => null,
    'entityName' => 'Item',
    'showKeepExisting' => false,
    'maxImages' => 10
])

@if($existingImages && $existingImages->count() > 0)
    <!-- Current Images -->
    <div class="modern-card mb-4">
        <div class="modern-card__header">
            <h3 class="modern-card__title">
                <i class="fas fa-images"></i>
                Current Images
            </h3>
        </div>
        <div class="modern-card__body">
            <div class="row g-3">
                @foreach($existingImages as $index => $image)
                    <div class="col-md-4 col-sm-6">
                        <div class="position-relative">
                            <img src="{{ $image->image_url ?? asset('storage/' . $image->image) }}"
                                 alt="{{ $entityName }} - Image {{ $index + 1 }}"
                                 class="img-fluid rounded shadow-sm"
                                 style="width: 100%; height: 150px; object-fit: cover;">
                            @if($index === 0)
                                <span class="position-absolute top-0 start-0 badge bg-primary m-2">Main</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if($showKeepExisting)
            <div class="mt-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="keep_existing_images" value="1" id="keepExistingImages" checked>
                    <label class="form-check-label" for="keepExistingImages">
                        Keep existing images when uploading new ones
                    </label>
                    <small class="form-text text-muted d-block">Uncheck to replace all images with new uploads</small>
                </div>
            </div>
            @endif
        </div>
    </div>
@endif

<!-- Image Upload Section -->
<div class="modern-card mb-4">
    <div class="modern-card__header">
        <h3 class="modern-card__title">
            <i class="fas fa-camera"></i>
            {{ $existingImages && $existingImages->count() > 0 ? 'Add More Images' : $label }}
        </h3>
    </div>
    <div class="modern-card__body">
        <div class="mb-3">
            <label for="{{ $id }}" class="form-label">{{ $label }}</label>
            <input type="file"
                   class="form-control @error($name) is-invalid @enderror @error($name.'.*') is-invalid @enderror"
                   id="{{ $id }}"
                   name="{{ $name }}[]"
                   multiple
                   accept="image/*">
            <small class="form-text text-muted">You can select multiple images (max {{ $maxImages }}). Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB per image.</small>
            @error($name)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @error($name.'.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div id="{{ $id }}PreviewContainer" class="row g-3"></div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    // Wait for DOM to be ready
    function initImagePreview() {
        const imageInput = document.getElementById('{{ $id }}');
        const imagePreviewContainer = document.getElementById('{{ $id }}PreviewContainer');

        if (!imageInput || !imagePreviewContainer) {
            // Retry if elements not found yet
            setTimeout(initImagePreview, 100);
            return;
        }
        imageInput.addEventListener('change', function() {
            imagePreviewContainer.innerHTML = '';

            if (this.files.length > 0) {
                Array.from(this.files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imageItem = createImagePreview(e.target.result, index);
                            imagePreviewContainer.appendChild(imageItem);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        function createImagePreview(src, index) {
            const div = document.createElement('div');
            div.className = 'col-md-4 col-sm-6';
            div.innerHTML = `
                <div class="image-preview-item" style="position: relative; margin-bottom: 1rem;">
                    <img src="${src}" alt="Preview ${index + 1}" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">
                    <button type="button" class="image-remove-btn" data-index="${index}" style="position: absolute; top: 5px; right: 5px; background: var(--danger-color); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            // Add remove functionality
            div.querySelector('.image-remove-btn').addEventListener('click', function() {
                const indexToRemove = parseInt(this.getAttribute('data-index'));
                removeImageFromInput(indexToRemove);
            });

            return div;
        }

        function removeImageFromInput(indexToRemove) {
            const dt = new DataTransfer();
            const files = imageInput.files;

            for (let i = 0; i < files.length; i++) {
                if (i !== indexToRemove) {
                    dt.items.add(files[i]);
                }
            }

            imageInput.files = dt.files;
            // Trigger change event to refresh preview
            imageInput.dispatchEvent(new Event('change'));
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initImagePreview);
    } else {
        initImagePreview();
    }
})();
</script>
@endpush
