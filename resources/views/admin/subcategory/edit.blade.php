@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">Edit Sub Category</h1>
                <p class="content-subtitle">Update sub category information</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Sub Categories
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sub Category Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.subcategories.update', $subcategory) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Parent Category *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $subcategory->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Sub Category Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $subcategory->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" name="slug" value="{{ old('slug', $subcategory->slug) }}">
                                <div class="form-text">If left empty, slug will be auto-generated from subcategory name</div>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="1" {{ old('status', $subcategory->status) === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $subcategory->status) === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($subcategory->image)
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label>
                                    <div>
                                        <img src="{{ $subcategory->image_url }}" alt="{{ $subcategory->name }}" 
                                             class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="image" class="form-label">
                                    {{ $subcategory->image ? 'Replace Image' : 'Sub Category Image' }}
                                </label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="imagePreview" style="display: none;">
                                <label class="form-label">New Image Preview</label>
                                <div>
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Sub Category
                                </button>
                                <a href="{{ route('admin.subcategories.show', $subcategory) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.subcategories.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sub Category Details</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">UUID:</dt>
                            <dd class="col-sm-8"><small class="text-muted">{{ $subcategory->uuid }}</small></dd>
                            
                            <dt class="col-sm-4">Category:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-info">{{ $subcategory->category->name }}</span>
                            </dd>
                            
                            <dt class="col-sm-4">Created:</dt>
                            <dd class="col-sm-8">{{ $subcategory->created_at->format('M d, Y g:i A') }}</dd>
                            
                            <dt class="col-sm-4">Updated:</dt>
                            <dd class="col-sm-8">{{ $subcategory->updated_at->format('M d, Y g:i A') }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                You can change the parent category
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                Changing the name will update the slug
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                Uploading a new image will replace the current one
                            </li>
                            <li>
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                Status changes take effect immediately
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function(e) {
    const slugField = document.getElementById('slug');
    const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    slugField.value = slug;
});
</script>
@endsection
