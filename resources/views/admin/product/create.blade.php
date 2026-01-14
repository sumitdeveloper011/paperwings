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
                                          id="short_description" name="short_description" rows="3" maxlength="500" data-required="true">{{ old('short_description') }}</textarea>
                                <small class="form-text text-muted">Maximum 500 characters</small>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="6" data-required="true">{{ old('description') }}</textarea>
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
                            <!-- Row 1: Total Price, Discount Type, Discount Field -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-4">
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
                                        <label for="discount_type" class="form-label">Discount Type</label>
                                        <select class="form-select" id="discount_type" name="discount_type">
                                            <option value="none" {{ old('discount_type', 'none') == 'none' ? 'selected' : '' }}>No Discount</option>
                                            <option value="direct" {{ old('discount_type') == 'direct' ? 'selected' : '' }}>Direct Price</option>
                                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Discount</label>
                                        <div id="discount_field_wrapper">
                                            <div class="input-group" id="discount_direct_wrapper" style="display: none;">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control @error('discount_price') is-invalid @enderror"
                                                       id="discount_price" name="discount_price" value="{{ old('discount_price') }}"
                                                       step="0.01" min="0" placeholder="0.00" disabled>
                                            </div>
                                            <div class="input-group" id="discount_percentage_wrapper" style="display: none;">
                                                <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror"
                                                       id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage') }}"
                                                       step="0.01" min="0" max="100" placeholder="0" disabled>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <small class="form-text text-muted" id="calculated_discount_price"></small>
                                            @error('discount_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @error('discount_percentage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">Optional: Set a discount using direct price or percentage</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Row 2: Price Without Tax, Tax Amount, Product Type -->
                            <div class="row">
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
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="product_type" class="form-label">Product Type</label>
                                        <select class="form-select @error('product_type') is-invalid @enderror"
                                                id="product_type" name="product_type">
                                            <option value="">Select Product Type</option>
                                            <option value="1" {{ old('product_type') == '1' ? 'selected' : '' }}>Featured</option>
                                            <option value="2" {{ old('product_type') == '2' ? 'selected' : '' }}>On Sale</option>
                                            <option value="3" {{ old('product_type') == '3' ? 'selected' : '' }}>Top Rated</option>
                                        </select>
                                        <small class="form-text text-muted">Optional: Categorize your product</small>
                                        @error('product_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion Data -->
                    @include('components.accordion-editor', [
                        'name' => 'accordion_data',
                        'existingAccordions' => null,
                        'oldAccordionData' => old('accordion_data')
                    ])

                    <!-- Product Images -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-images"></i>
                                Product Images
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <x-image-requirements type="product" />
                            
                            @include('components.multiple-image-upload', [
                                'name' => 'images',
                                'id' => 'images',
                                'label' => 'Upload Images',
                                'existingImages' => null,
                                'entityName' => 'Product',
                                'showKeepExisting' => false,
                                'maxImages' => 10
                            ])
                        </div>
                    </div>

                    <!-- SEO Meta Fields -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-search"></i>
                                SEO Meta Fields
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i>
                                <small>These fields are optional. They help improve search engine visibility. If left empty, they will be auto-generated from product information.</small>
                            </div>

                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                       id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                                       placeholder="Leave empty to use product name" maxlength="255">
                                <small class="form-text text-muted">Recommended: 50-60 characters. Leave empty to use product name.</small>
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                          id="meta_description" name="meta_description" rows="3" maxlength="500"
                                          placeholder="Leave empty to use short description">{{ old('meta_description') }}</textarea>
                                <small class="form-text text-muted">Recommended: 150-160 characters. Leave empty to use short description.</small>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror"
                                       id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}"
                                       placeholder="e.g., product, category, brand (comma separated)" maxlength="500">
                                <small class="form-text text-muted">Comma-separated keywords. Leave empty to auto-generate from product name and category.</small>
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Categories & Relationships -->
                <div class="col-lg-4">
                    <!-- Tips -->
                    <div class="modern-card mb-4">
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
                                        <strong>Product Name</strong>
                                        <p>Choose a clear and descriptive product name</p>
                                    </div>
                                </li>
                                <li class="tips-list__item">
                                    <i class="fas fa-check-circle"></i>
                                    <div>
                                        <strong>Short Description</strong>
                                        <p>Keep it concise (max 500 characters) and engaging</p>
                                    </div>
                                </li>
                                <li class="tips-list__item">
                                    <i class="fas fa-check-circle"></i>
                                    <div>
                                        <strong>Pricing</strong>
                                        <p>Enter the total price including tax (15%). Price without tax and tax amount are automatically calculated. You can set discounts using:</p>
                                        <ul style="margin-top: 0.5rem; margin-left: 1.5rem; font-size: 0.9em;">
                                            <li><strong>Direct Price:</strong> Set the final selling price directly. Example: Total price $100, Direct discount $80 = Customer pays $80</li>
                                            <li><strong>Percentage:</strong> Set discount percentage. Example: Total price $100, 20% discount = Customer pays $80</li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="tips-list__item">
                                    <i class="fas fa-check-circle"></i>
                                    <div>
                                        <strong>Product Type</strong>
                                        <p>Optional: Select "Featured" to highlight special products, "On Sale" for discounted items, or "Top Rated" for highly-rated products. This helps categorize and display products on the frontend.</p>
                                    </div>
                                </li>
                                <li class="tips-list__item">
                                    <i class="fas fa-check-circle"></i>
                                    <div>
                                        <strong>Images</strong>
                                        <p>Upload high-quality square images (1:1 ratio). Images will be automatically resized to 1200x1200 (original). Max 2MB each, up to 10 images.</p>
                                    </div>
                                </li>
                                <li class="tips-list__item">
                                    <i class="fas fa-check-circle"></i>
                                    <div>
                                        <strong>Category</strong>
                                        <p>Select the appropriate category for better organization</p>
                                    </div>
                                </li>
                                <li class="tips-list__item">
                                    <i class="fas fa-check-circle"></i>
                                    <div>
                                        <strong>Status</strong>
                                        <p>Set status to 'Active' to make it visible to customers</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Category & Brand Selection -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-tags"></i>
                                Categories & Brand
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            {{-- Category Select Component (Select2 Enabled) --}}
                            @include('components.select-category', [
                                'id' => 'category_id',
                                'name' => 'category_id',
                                'label' => 'Category',
                                'required' => true,
                                'selected' => old('category_id'),
                                'categories' => $categories,
                                'useUuid' => false,
                                'placeholder' => 'Select Category',
                                'useSelect2' => true
                            ])

                            {{-- Brand Select Component (Hidden for future use, Select2 ready) --}}
                            @include('components.select-brand', [
                                'id' => 'brand_id',
                                'name' => 'brand_id',
                                'label' => 'Brand',
                                'required' => false,
                                'selected' => old('brand_id'),
                                'brands' => $brands,
                                'placeholder' => 'Select Brand',
                                'useSelect2' => true,
                                'hidden' => true
                            ])

                            {{-- Subcategory Select Component (Hidden for future use, Select2 ready) --}}
                            @include('components.select-subcategory', [
                                'id' => 'subcategory_id',
                                'name' => 'subcategory_id',
                                'label' => 'Sub Category',
                                'required' => false,
                                'selected' => old('subcategory_id'),
                                'subcategories' => [],
                                'categoryId' => 'category_id',
                                'loadUrl' => route('admin.products.getSubCategories'),
                                'placeholder' => 'Select Sub Category',
                                'useSelect2' => true,
                                'hidden' => true
                            ])

                            {{-- Tags Select Component --}}
                            @include('components.select-tags', [
                                'id' => 'tag_ids',
                                'name' => 'tag_ids[]',
                                'label' => 'Tags',
                                'required' => false,
                                'selected' => old('tag_ids', []),
                                'tags' => $tags ?? [],
                                'placeholder' => 'Select tags...',
                                'helpText' => 'Select multiple tags for this product'
                            ])
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

    // Price calculations - Now handled by product-pricing.js

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

    // Form submission handler - Sync CKEditor content before submit
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            // Sync CKEditor content to textareas
            if (window.short_descriptionEditor) {
                const shortDescTextarea = document.getElementById('short_description');
                if (shortDescTextarea) {
                    shortDescTextarea.value = window.short_descriptionEditor.getData();
                }
            }

            if (window.descriptionEditor) {
                const descTextarea = document.getElementById('description');
                if (descTextarea) {
                    descTextarea.value = window.descriptionEditor.getData();
                }
            }

            // Accordion CKEditor instances are synced by the accordion-editor component
        });
    }
});

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
        document.getElementById('productForm').reset();
        // Accordion reset is handled by the accordion-editor component
        document.getElementById('imagePreviewContainer').innerHTML = '';
        document.getElementById('price_without_tax').value = '';
        document.getElementById('tax_amount').value = '';
        document.getElementById('subcategory_id').innerHTML = '<option value="">Select Sub Category</option>';
    }
}

// Select2 initialization - Now handled by reusable components
</script>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Product Pricing JS (Reusable) -->
<script src="{{ asset('assets/js/product-pricing.js') }}"></script>

<!-- CKEditor 5 - Custom build with SourceEditing -->
<script src="{{ asset('assets/js/ckeditor-custom.js') }}"></script>

<!-- CKEditor Component for Short Description -->
@include('components.ckeditor', [
    'id' => 'short_description',
    'uploadUrl' => route('admin.pages.uploadImage'),
    'toolbar' => 'basic'
])

<!-- CKEditor Component for Full Description -->
@include('components.ckeditor', [
    'id' => 'description',
    'uploadUrl' => route('admin.pages.uploadImage'),
    'toolbar' => 'full'
])
@endpush
@endsection
