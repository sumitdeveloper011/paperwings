<div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addImageModalLabel">
                    <i class="fas fa-image"></i>
                    Add Image to Gallery
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.gallery-items.store', $gallery) }}" enctype="multipart/form-data" id="addImageForm">
                @csrf
                <input type="hidden" name="type" value="image">
                <div class="modal-body">
                    <div class="form-group-modern">
                        <label for="image" class="form-label-modern">
                            Image File <span class="required">*</span>
                        </label>
                        <x-image-requirements type="gallery" />
                        <div class="file-upload-wrapper">
                            <input type="file"
                                   class="file-upload-input @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                   required>
                            <label for="image" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Choose Image</span>
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
                        <label class="form-label-modern">Image Preview</label>
                        <div class="image-preview">
                            <img id="previewImg" src="" alt="Preview" class="image-preview__img">
                            <button type="button" class="image-preview__remove" onclick="removeImagePreview()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label for="image_title" class="form-label-modern">
                            Title
                        </label>
                        <input type="text"
                               class="form-input-modern @error('title') is-invalid @enderror"
                               id="image_title"
                               name="title"
                               value="{{ old('title') }}"
                               placeholder="Enter image title"
                               maxlength="255">
                        @error('title')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label for="image_description" class="form-label-modern">
                            Description
                        </label>
                        <textarea class="form-input-modern @error('description') is-invalid @enderror"
                                  id="image_description"
                                  name="description"
                                  rows="3"
                                  placeholder="Enter image description"
                                  maxlength="2000">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label for="image_alt_text" class="form-label-modern">
                            Alt Text
                        </label>
                        <input type="text"
                               class="form-input-modern @error('alt_text') is-invalid @enderror"
                               id="image_alt_text"
                               name="alt_text"
                               value="{{ old('alt_text') }}"
                               placeholder="Enter alt text for accessibility"
                               maxlength="255">
                        @error('alt_text')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="is_featured" 
                                   value="1" 
                                   id="image_is_featured"
                                   class="form-check-input"
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="image_is_featured">
                                Set as Featured Image
                            </label>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Featured image will be used as gallery cover
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i>
                        Upload Image
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (imageInput && imagePreview && previewImg) {
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
</script>
