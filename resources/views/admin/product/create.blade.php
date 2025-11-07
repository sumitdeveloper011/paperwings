@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Create Product
                </h1>
                <p class="page-header__subtitle">Add a new product to your catalog</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Products</span>
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            
            <div class="row">
                <!-- Left Column - Main Product Information -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-info-circle"></i>
                                Basic Information
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
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
                                       id="slug" name="slug" value="{{ old('slug') }}">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="short_description" class="form-label">Short Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                          id="short_description" name="short_description" rows="3" maxlength="500" required>{{ old('short_description') }}</textarea>
                                <small class="form-text text-muted">Maximum 500 characters</small>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-dollar-sign"></i>
                                Pricing Information
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="total_price" class="form-label">Total Price (Including Tax) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('total_price') is-invalid @enderror" 
                                                   id="total_price" name="total_price" value="{{ old('total_price') }}" 
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
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <div class="modern-card__header-content">
                                <h3 class="modern-card__title">
                                    <i class="fas fa-list"></i>
                                    Additional Information (Accordion)
                                </h3>
                            </div>
                            <div class="modern-card__header-actions">
                                <button type="button" class="btn btn-sm btn-primary" id="addAccordionItem">
                                    <i class="fas fa-plus"></i> Add Section
                                </button>
                            </div>
                        </div>
                        <div class="modern-card__body">
                            <div id="accordionContainer">
                                @if(old('accordion_data'))
                                    @foreach(old('accordion_data') as $index => $item)
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
                            <div class="text-muted text-center py-3" id="emptyAccordionMessage" style="{{ old('accordion_data') ? 'display: none;' : '' }}">
                                <i class="fas fa-info-circle"></i> No additional sections added yet. Click "Add Section" to create accordion content.
                            </div>
                        </div>
                    </div>

                    <!-- Product Images -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-images"></i>
                                Product Images
                            </h3>
                        </div>
                        <div class="modern-card__body">
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
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-tags"></i>
                                Categories & Brand
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories->where('status', 1) as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    @if(old('category_id'))
                                        @foreach($subCategories->where('category_id', old('category_id'))->where('status', 1) as $subCategory)
                                            <option value="{{ $subCategory->id }}" {{ old('subcategory_id') == $subCategory->id ? 'selected' : '' }}>
                                                {{ $subCategory->name }}
                                            </option>
                                        @endforeach
                                    @endif
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
                                    @foreach($brands->where('status', 1) as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
                    <div class="modern-card">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-save"></i>
                                Actions
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Product
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset Form
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    let accordionCounter = {{ old('accordion_data') ? count(old('accordion_data')) : 0 }};
    
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        if (!slugInput.value) {
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
    
    // Initial calculation if there's an old value
    if (totalPriceInput.value) {
        calculatePrices();
    }

    // Dynamic subcategory loading
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');

    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
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
    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
        document.getElementById('productForm').reset();
        document.getElementById('accordionContainer').innerHTML = '';
        document.getElementById('imagePreviewContainer').innerHTML = '';
        document.getElementById('emptyAccordionMessage').style.display = 'block';
        document.getElementById('price_without_tax').value = '';
        document.getElementById('tax_amount').value = '';
        document.getElementById('subcategory_id').innerHTML = '<option value="">Select Sub Category</option>';
    }
}
</script>
@endsection