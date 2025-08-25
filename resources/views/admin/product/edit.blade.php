@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">Edit Product</h1>
                <p class="content-subtitle">Update product: {{ $product->name }}</p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> View Product
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Left Column - Main Product Information -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <small class="text-muted">(Leave empty to auto-generate)</small></label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="short_description" class="form-label">Short Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                          id="short_description" name="short_description" rows="3" maxlength="500" required>{{ old('short_description', $product->short_description) }}</textarea>
                                <small class="form-text text-muted">Maximum 500 characters</small>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="6" required>{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-dollar-sign me-2"></i>Pricing Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="total_price" class="form-label">Total Price (Including Tax) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('total_price') is-invalid @enderror" 
                                                   id="total_price" name="total_price" value="{{ old('total_price', $product->total_price) }}" 
                                                   step="0.01" min="0" required>
                                        </div>
                                        @error('total_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Price Without Tax</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control bg-light" id="price_without_tax" readonly>
                                        </div>
                                        <small class="form-text text-muted">Automatically calculated</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Tax Amount (15%)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control bg-light" id="tax_amount" readonly>
                                        </div>
                                        <small class="form-text text-muted">Automatically calculated</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion Data -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Additional Information (Accordion)
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addAccordionItem">
                                <i class="fas fa-plus"></i> Add Section
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="accordionContainer">
                                @php
                                    $accordionData = old('accordion_data', $product->accordion_data ?? []);
                                @endphp
                                @if($accordionData && count($accordionData) > 0)
                                    @foreach($accordionData as $index => $item)
                                        <div class="accordion-item-wrapper mb-3 border rounded p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Section {{ $index + 1 }}</h6>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-accordion-item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="mb-2">
                                                <input type="text" class="form-control" name="accordion_data[{{ $index }}][heading]" 
                                                       placeholder="Section heading..." value="{{ $item['heading'] ?? '' }}">
                                            </div>
                                            <div>
                                                <textarea class="form-control" name="accordion_data[{{ $index }}][content]" 
                                                          rows="3" placeholder="Section content...">{{ $item['content'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="text-muted text-center py-3" id="emptyAccordionMessage" style="{{ $accordionData && count($accordionData) > 0 ? 'display: none;' : '' }}">
                                <i class="fas fa-info-circle"></i> No additional sections added yet. Click "Add Section" to create accordion content.
                            </div>
                        </div>
                    </div>

                    <!-- Current Images -->
                    @if($product->images && count($product->images) > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-images me-2"></i>Current Images
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($product->images as $index => $image)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="position-relative">
                                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }} - Image {{ $index + 1 }}" 
                                                 class="img-fluid rounded shadow-sm" style="width: 100%; height: 150px; object-fit: cover;">
                                            @if($index === 0)
                                                <span class="position-absolute top-0 start-0 badge bg-primary m-2">Main</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="keep_existing_images" value="1" id="keepExistingImages" checked>
                                    <label class="form-check-label" for="keepExistingImages">
                                        Keep existing images when uploading new ones
                                    </label>
                                    <small class="form-text text-muted d-block">Uncheck to replace all images with new uploads</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Product Images -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-camera me-2"></i>{{ $product->images && count($product->images) > 0 ? 'Add More Images' : 'Upload Images' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="images" class="form-label">Upload Images</label>
                                <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" 
                                       id="images" name="images[]" multiple accept="image/*">
                                <small class="form-text text-muted">You can select multiple images (max 10). Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB per image.</small>
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div id="imagePreviewContainer" class="row g-3"></div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Categories & Relationships -->
                <div class="col-lg-4">
                    <!-- Category & Brand Selection -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tags me-2"></i>Categories & Brand
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="subcategory_id" class="form-label">Sub Category</label>
                                <select class="form-select @error('subcategory_id') is-invalid @enderror" 
                                        id="subcategory_id" name="subcategory_id">
                                    <option value="">Select Sub Category</option>
                                    @foreach($subCategories->where('category_id', old('category_id', $product->category_id)) as $subCategory)
                                        <option value="{{ $subCategory->id }}" {{ old('subcategory_id', $product->subcategory_id) == $subCategory->id ? 'selected' : '' }}>
                                            {{ $subCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subcategory_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="brand_id" class="form-label">Brand</label>
                                <select class="form-select @error('brand_id') is-invalid @enderror" 
                                        id="brand_id" name="brand_id">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-save me-2"></i>Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Product
                                </button>
                                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-2"></i>View Product
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset Changes
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.accordion-item-wrapper {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.accordion-item-wrapper:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.image-preview-item {
    position: relative;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.image-preview-item:hover {
    border-color: #0d6efd;
}

.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.image-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-remove-btn:hover {
    background: rgba(220, 53, 69, 1);
}

#imagePreviewContainer:empty::after {
    content: "No new images selected. Choose images using the file input above.";
    display: block;
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 2rem;
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    background-color: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let accordionCounter = {{ count($accordionData ?? []) }};
    
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const originalSlug = '{{ $product->slug }}';
    
    nameInput.addEventListener('input', function() {
        if (slugInput.value === originalSlug || !slugInput.value) {
            slugInput.value = this.value.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        }
    });

    // Price calculations
    const totalPriceInput = document.getElementById('total_price');
    const priceWithoutTaxInput = document.getElementById('price_without_tax');
    const taxAmountInput = document.getElementById('tax_amount');

    function calculatePrices() {
        const totalPrice = parseFloat(totalPriceInput.value) || 0;
        const priceWithoutTax = totalPrice / 1.15;
        const taxAmount = totalPrice - priceWithoutTax;

        priceWithoutTaxInput.value = priceWithoutTax.toFixed(2);
        taxAmountInput.value = taxAmount.toFixed(2);
    }

    totalPriceInput.addEventListener('input', calculatePrices);
    
    // Initial calculation
    calculatePrices();

    // Dynamic subcategory loading
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');

    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        const currentSubcategoryId = '{{ old("subcategory_id", $product->subcategory_id) }}';
        
        // Clear subcategory options
        subcategorySelect.innerHTML = '<option value="">Select Sub Category</option>';
        
        if (categoryId) {
            fetch(`{{ route('admin.products.getSubCategories') }}?category_id=${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(subCategory => {
                        const option = document.createElement('option');
                        option.value = subCategory.id;
                        option.textContent = subCategory.name;
                        if (subCategory.id == currentSubcategoryId) {
                            option.selected = true;
                        }
                        subcategorySelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading subcategories:', error));
        }
    });

    // Accordion management
    const addAccordionBtn = document.getElementById('addAccordionItem');
    const accordionContainer = document.getElementById('accordionContainer');
    const emptyMessage = document.getElementById('emptyAccordionMessage');

    addAccordionBtn.addEventListener('click', function() {
        const accordionItem = createAccordionItem(accordionCounter);
        accordionContainer.appendChild(accordionItem);
        accordionCounter++;
        updateAccordionVisibility();
    });

    function createAccordionItem(index) {
        const div = document.createElement('div');
        div.className = 'accordion-item-wrapper mb-3 border rounded p-3';
        div.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Section ${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-accordion-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-2">
                <input type="text" class="form-control" name="accordion_data[${index}][heading]" 
                       placeholder="Section heading...">
            </div>
            <div>
                <textarea class="form-control" name="accordion_data[${index}][content]" 
                          rows="3" placeholder="Section content..."></textarea>
            </div>
        `;

        // Add remove functionality
        div.querySelector('.remove-accordion-item').addEventListener('click', function() {
            div.remove();
            updateAccordionVisibility();
            reindexAccordionItems();
        });

        return div;
    }

    function updateAccordionVisibility() {
        const hasItems = accordionContainer.children.length > 0;
        emptyMessage.style.display = hasItems ? 'none' : 'block';
    }

    function reindexAccordionItems() {
        Array.from(accordionContainer.children).forEach((item, index) => {
            item.querySelector('h6').textContent = `Section ${index + 1}`;
            const inputs = item.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
        });
        accordionCounter = accordionContainer.children.length;
    }

    // Add remove functionality to existing accordion items
    document.querySelectorAll('.remove-accordion-item').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.accordion-item-wrapper').remove();
            updateAccordionVisibility();
            reindexAccordionItems();
        });
    });

    // Image preview functionality
    const imageInput = document.getElementById('images');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');

    imageInput.addEventListener('change', function() {
        imagePreviewContainer.innerHTML = '';
        
        if (this.files.length > 0) {
            Array.from(this.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imageItem = createImagePreview(e.target.result, index);
                        imagePreviewContainer.appendChild(imageItem);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    function createImagePreview(src, index) {
        const div = document.createElement('div');
        div.className = 'col-md-4 col-sm-6';
        div.innerHTML = `
            <div class="image-preview-item">
                <img src="${src}" alt="Preview ${index + 1}">
                <button type="button" class="image-remove-btn" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Add remove functionality
        div.querySelector('.image-remove-btn').addEventListener('click', function() {
            const indexToRemove = parseInt(this.getAttribute('data-index'));
            removeImageFromInput(indexToRemove);
        });

        return div;
    }

    function removeImageFromInput(indexToRemove) {
        const dt = new DataTransfer();
        const files = imageInput.files;

        for (let i = 0; i < files.length; i++) {
            if (i !== indexToRemove) {
                dt.items.add(files[i]);
            }
        }

        imageInput.files = dt.files;
        // Trigger change event to refresh preview
        imageInput.dispatchEvent(new Event('change'));
    }

    // Initial accordion visibility check
    updateAccordionVisibility();
});

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All changes will be lost.')) {
        location.reload();
    }
}
</script>
@endsection