@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Brand
                </h1>
                <p class="page-header__subtitle">Update brand information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-outline-info btn-icon">
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                </a>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Brands</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Brand Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data" class="modern-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">
                                Brand Name <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-award input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $brand->name) }}" 
                                       placeholder="Enter brand name"
                                       required>
                            </div>
                            @error('name')
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
                                       value="{{ old('slug', $brand->slug) }}" 
                                       placeholder="brand-slug">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                If left empty, slug will be auto-generated from brand name
                            </div>
                            @error('slug')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        @if($brand->image)
                            <div class="form-group-modern">
                                <label class="form-label-modern">Current Logo</label>
                                <div class="current-image">
                                    <img src="{{ $brand->image_url }}" 
                                         alt="{{ $brand->name }}" 
                                         class="current-image__img">
                                </div>
                            </div>
                        @endif

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">
                                {{ $brand->image ? 'Replace Logo/Image' : 'Brand Logo/Image' }}
                            </label>
                            <div class="file-upload-wrapper">
                                <input type="file" 
                                       class="file-upload-input @error('image') is-invalid @enderror" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*">
                                <label for="image" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>{{ $brand->image ? 'Replace Logo' : 'Choose Logo' }}</span>
                                </label>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB
                            </div>
                            @error('image')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern" id="imagePreview" style="display: none;">
                            <label class="form-label-modern">New Logo Preview</label>
                            <div class="image-preview">
                                <img id="previewImg" src="" alt="Preview" class="image-preview__img">
                                <button type="button" class="image-preview__remove" onclick="removeImagePreview()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i>
                                Update Brand
                            </button>
                            <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-outline-info btn-lg">
                                <i class="fas fa-eye"></i>
                                View
                            </a>
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary btn-lg">
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
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Brand Details
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-fingerprint"></i>
                                UUID
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block code-block--small">{{ $brand->uuid }}</code>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-plus-circle"></i>
                                Created
                            </div>
                            <div class="detail-item__value">
                                {{ $brand->created_at->format('M d, Y g:i A') }}
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-edit"></i>
                                Updated
                            </div>
                            <div class="detail-item__value">
                                {{ $brand->updated_at->format('M d, Y g:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card">
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
                                <strong>Name Update</strong>
                                <p>Changing the name will update the slug</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Logo Replacement</strong>
                                <p>Uploading a new logo will replace the current one</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Consistency</strong>
                                <p>Maintain brand consistency across updates</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

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

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function(e) {
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
