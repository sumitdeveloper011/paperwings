@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Page
                </h1>
                <p class="page-header__subtitle">Update page: {{ $page->title }}</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-outline-info btn-icon">
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                </a>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Pages</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Page Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data" class="modern-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group-modern">
                            <label for="title" class="form-label-modern">
                                Title <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $page->title) }}" 
                                       placeholder="Enter page title"
                                       required>
                            </div>
                            @error('title')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="slug" class="form-label-modern">
                                Slug
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-link input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('slug') is-invalid @enderror" 
                                       id="slug" 
                                       name="slug" 
                                       value="{{ old('slug', $page->slug) }}" 
                                       placeholder="page-slug (auto-generated if empty)">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                If left empty, slug will be auto-generated from page title
                            </div>
                            @error('slug')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="sub_title" class="form-label-modern">
                                Sub Title
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-text-height input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('sub_title') is-invalid @enderror" 
                                       id="sub_title" 
                                       name="sub_title" 
                                       value="{{ old('sub_title', $page->sub_title) }}" 
                                       placeholder="Enter sub title (optional)">
                            </div>
                            @error('sub_title')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="content" class="form-label-modern">
                                Content
                            </label>
                            <textarea class="form-input-modern @error('content') is-invalid @enderror" 
                                      id="content" 
                                      name="content" 
                                      rows="25"
                                      placeholder="Enter page content">{{ old('content', $page->content) }}</textarea>
                            @error('content')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Current Image -->
                        @if($page->image)
                        <div class="form-group-modern">
                            <label class="form-label-modern">Current Banner Image</label>
                            <div class="image-preview">
                                <img src="{{ $page->image_url }}" alt="{{ $page->title }}" class="image-preview__img">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Current image will be replaced if you upload a new one
                            </div>
                        </div>
                        @endif

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">
                                {{ $page->image ? 'Update Banner Image' : 'Banner Image' }}
                            </label>
                            <div class="file-upload-wrapper">
                                <input type="file" 
                                       class="file-upload-input @error('image') is-invalid @enderror" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*">
                                <label for="image" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose {{ $page->image ? 'New ' : '' }}Image</span>
                                </label>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                {{ $page->image ? 'Leave empty to keep current image. ' : '' }}Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB
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

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg btn-ripple">
                                <i class="fas fa-save"></i>
                                Update Page
                            </button>
                            <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-outline-info btn-lg">
                                <i class="fas fa-eye"></i>
                                View Page
                            </a>
                            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-lightbulb"></i>
                        Tips
                    </h3>
                </div>
                <div class="modern-card__body">
                    <ul class="tips-list">
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Clear Title</strong>
                                <p>Choose a clear and descriptive page title</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Auto Slug</strong>
                                <p>Slug will be auto-generated if left empty</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Rich Content</strong>
                                <p>Use the editor to format your content beautifully</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Banner Image</strong>
                                <p>Add a banner image to make your page more attractive</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor 5 with Image Upload Plugin -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/translations/en.js"></script>

<script>
// Initialize CKEditor with Image Upload
ClassicEditor
    .create(document.querySelector('#content'), {
        height: 600,
        toolbar: {
            items: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'imageUpload', 'blockQuote', 'insertTable', '|',
                'undo', 'redo'
            ]
        },
        language: 'en',
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
            ]
        },
        simpleUpload: {
            uploadUrl: '{{ route("admin.pages.uploadImage") }}',
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        image: {
            toolbar: [
                'imageTextAlternative',
                'imageStyle:inline',
                'imageStyle:block',
                'imageStyle:side'
            ]
        }
    })
    .then(editor => {
        window.editor = editor;
    })
    .catch(error => {
        console.error('CKEditor initialization error:', error);
        // Fallback: If imageUpload doesn't work, remove it and show a message
        if (error.message && error.message.includes('imageUpload')) {
            console.warn('Image upload plugin not available. Using basic editor.');
            ClassicEditor
                .create(document.querySelector('#content'), {
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'link', '|',
                            'bulletedList', 'numberedList', '|',
                            'outdent', 'indent', '|',
                            'blockQuote', 'insertTable', '|',
                            'undo', 'redo'
                        ]
                    }
                })
                .catch(err => console.error(err));
        }
    });

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

// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function(e) {
    const slugField = document.getElementById('slug');
    if (!slugField.value || slugField.dataset.autoGenerated === 'true') {
        const slug = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        slugField.value = slug;
        slugField.dataset.autoGenerated = 'true';
    }
});

// Mark slug as manually edited
document.getElementById('slug').addEventListener('input', function() {
    this.dataset.autoGenerated = 'false';
});
</script>
@endsection

