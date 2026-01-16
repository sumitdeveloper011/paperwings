<div class="modal fade" id="addVideoModal" tabindex="-1" aria-labelledby="addVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVideoModalLabel">
                    <i class="fas fa-video"></i>
                    Add Video to Gallery
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.gallery-items.store', $gallery) }}" enctype="multipart/form-data" id="addVideoForm">
                @csrf
                <input type="hidden" name="type" value="video">
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="videoTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="embed-tab" data-bs-toggle="tab" data-bs-target="#embed-pane" type="button" role="tab">
                                <i class="fas fa-code"></i>
                                Embed Code
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="url-tab" data-bs-toggle="tab" data-bs-target="#url-pane" type="button" role="tab">
                                <i class="fas fa-link"></i>
                                Video URL
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="videoTabContent">
                        <div class="tab-pane fade show active" id="embed-pane" role="tabpanel">
                            <div class="form-group-modern">
                                <label for="video_embed_code" class="form-label-modern">
                                    Embed Code <span class="required">*</span>
                                </label>
                                <textarea class="form-input-modern @error('video_embed_code') is-invalid @enderror"
                                          id="video_embed_code"
                                          name="video_embed_code"
                                          rows="4"
                                          placeholder="Paste YouTube or Vimeo iframe embed code here"
                                          maxlength="2000">{{ old('video_embed_code') }}</textarea>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    Paste the iframe embed code from YouTube or Vimeo
                                </div>
                                @error('video_embed_code')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="tab-pane fade" id="url-pane" role="tabpanel">
                            <div class="form-group-modern">
                                <label for="video_url" class="form-label-modern">
                                    Video URL <span class="required">*</span>
                                </label>
                                <input type="url"
                                       class="form-input-modern @error('video_url') is-invalid @enderror"
                                       id="video_url"
                                       name="video_url"
                                       value="{{ old('video_url') }}"
                                       placeholder="https://example.com/video.mp4"
                                       maxlength="500">
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    Direct video URL (MP4, WebM, OGG formats)
                                </div>
                                @error('video_url')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label for="video_thumbnail" class="form-label-modern">
                            Thumbnail Image
                        </label>
                        <div class="file-upload-wrapper">
                            <input type="file"
                                   class="file-upload-input @error('thumbnail') is-invalid @enderror"
                                   id="video_thumbnail"
                                   name="thumbnail"
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                            <label for="video_thumbnail" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Choose Thumbnail</span>
                            </label>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Optional: Upload a custom thumbnail for the video. Max size: 2MB
                        </div>
                        @error('thumbnail')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern" id="videoThumbnailPreview" style="display: none;">
                        <label class="form-label-modern">Thumbnail Preview</label>
                        <div class="image-preview">
                            <img id="previewVideoThumbnail" src="" alt="Preview" class="image-preview__img">
                            <button type="button" class="image-preview__remove" onclick="removeVideoThumbnailPreview()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label for="video_title" class="form-label-modern">
                            Title
                        </label>
                        <input type="text"
                               class="form-input-modern @error('title') is-invalid @enderror"
                               id="video_title"
                               name="title"
                               value="{{ old('title') }}"
                               placeholder="Enter video title"
                               maxlength="255">
                        @error('title')
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group-modern">
                        <label for="video_description" class="form-label-modern">
                            Description
                        </label>
                        <textarea class="form-input-modern @error('description') is-invalid @enderror"
                                  id="video_description"
                                  name="description"
                                  rows="3"
                                  placeholder="Enter video description"
                                  maxlength="2000">{{ old('description') }}</textarea>
                        @error('description')
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
                                   id="video_is_featured"
                                   class="form-check-input"
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="video_is_featured">
                                Set as Featured Video
                            </label>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Featured video will be used as gallery cover
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Video
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/admin-gallery-video-form.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    AdminGalleryVideoForm.init('addVideoForm');

    const videoThumbnailInput = document.getElementById('video_thumbnail');
    const videoThumbnailPreview = document.getElementById('videoThumbnailPreview');
    const previewVideoThumbnail = document.getElementById('previewVideoThumbnail');

    if (videoThumbnailInput && videoThumbnailPreview && previewVideoThumbnail) {
        videoThumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewVideoThumbnail.src = e.target.result;
                    videoThumbnailPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                videoThumbnailPreview.style.display = 'none';
            }
        });
    }
});

function removeVideoThumbnailPreview() {
    const videoThumbnailInput = document.getElementById('video_thumbnail');
    const videoThumbnailPreview = document.getElementById('videoThumbnailPreview');
    if (videoThumbnailInput) {
        videoThumbnailInput.value = '';
    }
    if (videoThumbnailPreview) {
        videoThumbnailPreview.style.display = 'none';
    }
}
</script>
@endpush
