@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Sub Category
                </h1>
                <p class="page-header__subtitle">Update subcategory information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.subcategories.show', $subcategory) }}" class="btn btn-outline-info btn-icon">
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                </a>
                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Sub Categories</span>
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
                        Subcategory Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.subcategories.update', $subcategory) }}" enctype="multipart/form-data" class="modern-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group-modern">
                            <label for="category_id" class="form-label-modern">
                                Parent Category <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-tag input-icon"></i>
                                <select class="form-input-modern @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $subcategory->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">
                                Sub Category Name <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-tags input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $subcategory->name) }}" 
                                       placeholder="Enter subcategory name"
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
                                       value="{{ old('slug', $subcategory->slug) }}" 
                                       placeholder="subcategory-slug">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                If left empty, slug will be auto-generated from subcategory name
                            </div>
                            @error('slug')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="status" class="form-label-modern">
                                Status <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select class="form-input-modern @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="">Select Status</option>
                                    <option value="1" {{ old('status', $subcategory->status) === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $subcategory->status) === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        @if($subcategory->image)
                            <div class="form-group-modern">
                                <label class="form-label-modern">Current Image</label>
                                <div class="current-image">
                                    <img src="{{ $subcategory->image_url }}" 
                                         alt="{{ $subcategory->name }}" 
                                         class="current-image__img">
                                </div>
                            </div>
                        @endif

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">
                                {{ $subcategory->image ? 'Replace Image' : 'Sub Category Image' }}
                            </label>
                            <div class="file-upload-wrapper">
                                <input type="file" 
                                       class="file-upload-input @error('image') is-invalid @enderror" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*">
                                <label for="image" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>{{ $subcategory->image ? 'Replace Image' : 'Choose Image' }}</span>
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
                            <label class="form-label-modern">New Image Preview</label>
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
                                Update Sub Category
                            </button>
                            <a href="{{ route('admin.subcategories.show', $subcategory) }}" class="btn btn-outline-info btn-lg">
                                <i class="fas fa-eye"></i>
                                View
                            </a>
                            <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary btn-lg">
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
                        Subcategory Details
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
                                <code class="code-block code-block--small">{{ $subcategory->uuid }}</code>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-tag"></i>
                                Parent Category
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge--info">{{ $subcategory->category->name }}</span>
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-plus-circle"></i>
                                Created
                            </div>
                            <div class="detail-item__value">
                                {{ $subcategory->created_at->format('M d, Y g:i A') }}
                            </div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-edit"></i>
                                Updated
                            </div>
                            <div class="detail-item__value">
                                {{ $subcategory->updated_at->format('M d, Y g:i A') }}
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
                                <strong>Parent Category</strong>
                                <p>You can change the parent category</p>
                            </div>
                        </li>
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
                                <strong>Image Replacement</strong>
                                <p>Uploading a new image will replace the current one</p>
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
