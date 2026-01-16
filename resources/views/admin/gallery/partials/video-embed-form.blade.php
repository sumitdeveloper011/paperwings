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
                                <div class="input-wrapper">
                                    <i class="fas fa-code input-icon"></i>
                                    <textarea class="form-input-modern @error('video_embed_code') is-invalid @enderror"
                                              id="video_embed_code"
                                              name="video_embed_code"
                                              rows="4"
                                              placeholder="Paste YouTube or Vimeo iframe embed code here"></textarea>
                                </div>
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
                                <div class="input-wrapper">
                                    <i class="fas fa-link input-icon"></i>
                                    <input type="url"
                                           class="form-input-modern @error('video_url') is-invalid @enderror"
                                           id="video_url"
                                           name="video_url"
                                           placeholder="https://example.com/video.mp4">
                                </div>
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
                        <div class="input-wrapper">
                            <input type="file"
                                   class="form-input-modern"
                                   id="video_thumbnail"
                                   name="thumbnail"
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Optional: Upload a custom thumbnail for the video
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label for="video_title" class="form-label-modern">
                            Title
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-heading input-icon"></i>
                            <input type="text"
                                   class="form-input-modern"
                                   id="video_title"
                                   name="title"
                                   placeholder="Enter video title">
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label for="video_description" class="form-label-modern">
                            Description
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-align-left input-icon"></i>
                            <textarea class="form-input-modern"
                                      id="video_description"
                                      name="description"
                                      rows="3"
                                      placeholder="Enter video description"></textarea>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <input type="checkbox" name="is_featured" value="1" id="video_is_featured">
                            Set as Featured Video
                        </label>
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
});
</script>
@endpush
