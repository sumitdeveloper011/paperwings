@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-info-circle"></i>
                    About Section
                </h1>
                <p class="page-header__subtitle">Manage homepage about section widget</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">About Section Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.about-sections.update') }}" class="modern-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group-modern">
                            <label for="badge" class="form-label-modern">Badge</label>
                            <div class="input-wrapper">
                                <i class="fas fa-tag input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('badge') is-invalid @enderror"
                                       id="badge"
                                       name="badge"
                                       value="{{ old('badge', $aboutSection->badge) }}"
                                       placeholder="e.g., THE STATIONERO">
                            </div>
                            @error('badge')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="title" class="form-label-modern">Title <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $aboutSection->title) }}"
                                       placeholder="Enter section title"
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
                                      rows="6"
                                      data-required="false"
                                      placeholder="Enter section description">{{ old('description', $aboutSection->description) }}</textarea>
                            @error('description')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">Image</label>
                            <x-image-requirements type="about-section" />
                            @if($aboutSection->image_url)
                                <div class="form-group-modern mb-3">
                                    <label class="form-label-modern">Current Image</label>
                                    <div class="image-preview">
                                        <img src="{{ $aboutSection->image_url }}" alt="{{ $aboutSection->title }}" class="image-preview__img">
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
                                    <span>Choose {{ $aboutSection->image_url ? 'New ' : '' }}Image</span>
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
                                               value="{{ old('button_text', $aboutSection->button_text) }}"
                                               placeholder="e.g., Find Out More">
                                    </div>
                                    @error('button_text')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                @include('components.smart-link-selector', [
                                    'name' => 'button_link',
                                    'id' => 'button_link',
                                    'label' => 'Button Link',
                                    'value' => old('button_link', $aboutSection->button_link),
                                    'required' => false,
                                    'categories' => $categories ?? collect(),
                                    'bundles' => $bundles ?? collect(),
                                    'pages' => $pages ?? collect(),
                                ])
                                @error('button_link')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="status" class="form-label-modern">Status <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select class="form-input-modern @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', $aboutSection->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $aboutSection->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <small class="form-text text-muted">When active, this section will be displayed on the homepage.</small>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update About Section
                            </button>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> This section appears on the homepage. For detailed company information, use the <a href="{{ route('admin.pages.index') }}" target="_blank">About Us page</a> in the Pages section.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor 5 - Custom build with SourceEditing -->
<script src="{{ asset('assets/js/ckeditor-custom.js') }}"></script>

<!-- CKEditor Component for Description -->
@include('components.ckeditor', [
    'id' => 'description',
    'uploadUrl' => route('admin.pages.uploadImage'),
    'toolbar' => 'full'
])

<script>
// Image Preview
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

function removeImagePreview() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}
</script>
@endsection

