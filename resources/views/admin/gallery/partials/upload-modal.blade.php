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
                        <div class="input-wrapper">
                            <input type="file"
                                   class="form-input-modern @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                   required>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Accepted formats: JPEG, JPG, PNG, GIF, WebP. Max size: 5MB
                        </div>
                        @error('image')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label for="image_title" class="form-label-modern">
                            Title
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-heading input-icon"></i>
                            <input type="text"
                                   class="form-input-modern"
                                   id="image_title"
                                   name="title"
                                   placeholder="Enter image title">
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label for="image_description" class="form-label-modern">
                            Description
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-align-left input-icon"></i>
                            <textarea class="form-input-modern"
                                      id="image_description"
                                      name="description"
                                      rows="3"
                                      placeholder="Enter image description"></textarea>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label for="image_alt_text" class="form-label-modern">
                            Alt Text
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-tag input-icon"></i>
                            <input type="text"
                                   class="form-input-modern"
                                   id="image_alt_text"
                                   name="alt_text"
                                   placeholder="Enter alt text for accessibility">
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <input type="checkbox" name="is_featured" value="1" id="image_is_featured">
                            Set as Featured Image
                        </label>
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
