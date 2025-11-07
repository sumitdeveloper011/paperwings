@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Sub Category
                </h1>
                <p class="page-header__subtitle">Create a new product subcategory</p>
            </div>
            <div class="page-header__actions">
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
                        Sub Category Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.subcategories.store') }}" enctype="multipart/form-data" class="modern-form">
                        @csrf
                        
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
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                       value="{{ old('name') }}" 
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
                                       value="{{ old('slug') }}" 
                                       placeholder="subcategory-slug (auto-generated if empty)">
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
                                    <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">
                                Sub Category Image
                            </label>
                            <div class="file-upload-wrapper">
                                <input type="file" 
                                       class="file-upload-input @error('image') is-invalid @enderror" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*">
                                <label for="image" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose Image</span>
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
                            <label class="form-label-modern">Image Preview</label>
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
                                Create Sub Category
                            </button>
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
                                <p>Select the parent category first</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Descriptive Name</strong>
                                <p>Choose a clear and descriptive subcategory name</p>
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
                                <strong>Quality Images</strong>
                                <p>Use high-quality images for better presentation</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            @if($categories->count() > 0)
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-tags"></i>
                            Available Categories
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="category-list">
                            @foreach($categories as $category)
                                <div class="category-list__item">
                                    <i class="fas fa-tag"></i>
                                    <span>{{ $category->name }}</span>
                                    @if($category->status === '1')
                                        <span class="badge badge--success badge--sm">Active</span>
                                    @else
                                        <span class="badge badge--danger badge--sm">Inactive</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
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
