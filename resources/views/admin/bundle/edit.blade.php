@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Product Bundle
                </h1>
                <p class="page-header__subtitle">Update bundle details</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="content-body">
        <form method="POST" action="{{ route('admin.bundles.update', $bundle) }}" enctype="multipart/form-data" id="bundleForm">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Left Column - Main Bundle Information -->
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
                            <div class="mb-3">
                                <label for="name" class="form-label">Bundle Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $bundle->name) }}" 
                                       placeholder="Enter bundle name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Enter bundle description">{{ old('description', $bundle->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Bundle Image</label>
                                @if($bundle->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $bundle->image) }}" 
                                             alt="{{ $bundle->name }}" 
                                             id="currentImagePreview"
                                             style="max-width: 200px; border-radius: 5px;">
                                    </div>
                                @endif
                                <input type="file" 
                                       class="form-control @error('image') is-invalid @enderror" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*"
                                       onchange="previewImage(this, 'imagePreview')">
                                <small class="form-text text-muted">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB.</small>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img src="" alt="Preview" style="max-width: 200px; border-radius: 5px;">
                                </div>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Product Selection -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-box"></i>
                                Product Selection
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <div class="mb-3">
                                <label for="category_filter" class="form-label">Filter by Category (Optional)</label>
                                <select class="form-select" id="category_filter">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Select a category to filter products</small>
                            </div>

                            <div class="mb-3">
                                <label for="product_ids" class="form-label">Select Products <span class="text-danger">*</span></label>
                                <select class="form-select" id="product_ids" name="product_ids[]" multiple required>
                                    @foreach($bundle->products as $product)
                                        <option value="{{ $product->id }}" selected data-price="{{ $product->total_price }}" data-name="{{ $product->name }}">
                                            {{ $product->name }} - ${{ number_format($product->total_price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Search and select at least 2 products. Type to search products.</small>
                                @error('product_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selected Products List -->
                            <div id="selectedProductsList" class="selected-products-list">
                                <h6 class="mb-2">Selected Products:</h6>
                                <div id="selectedProductsContainer" class="selected-products-container">
                                    <!-- Will be populated by JavaScript -->
                                </div>
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
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Select products first to see the total value. Then set your bundle price below. Discount percentage will be calculated automatically.
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bundle_price" class="form-label">Bundle Price (NZD) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control @error('bundle_price') is-invalid @enderror" 
                                               id="bundle_price" 
                                               name="bundle_price" 
                                               value="{{ old('bundle_price', $bundle->bundle_price) }}" 
                                               step="0.01"
                                               min="0"
                                               placeholder="0.00"
                                               required>
                                        <small class="form-text text-muted">Set the price customers will pay for this bundle</small>
                                        @error('bundle_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_percentage" class="form-label">Discount Percentage (Auto-calculated)</label>
                                        <input type="text"
                                               class="form-control"
                                               id="discount_percentage"
                                               name="discount_percentage"
                                               value="{{ old('discount_percentage', $bundle->discount_percentage) }}"
                                               readonly
                                               style="background-color: #f8f9fa; cursor: not-allowed;">
                                        <small class="form-text text-muted">Automatically calculated based on total products price vs bundle price</small>
                                        <input type="hidden" id="discount_percentage_hidden" name="discount_percentage">
                                        @error('discount_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="pricing-summary">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                            <div>
                                                <strong>Total Products Value:</strong>
                                                <span id="totalProductsValue" class="text-primary ms-2">$0.00</span>
                                            </div>
                                            <div>
                                                <strong>Bundle Price:</strong>
                                                <span id="displayBundlePrice" class="text-success ms-2">$0.00</span>
                                            </div>
                                            <div>
                                                <strong>Customer Saves:</strong>
                                                <span id="customerSavings" class="text-danger ms-2">$0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Settings -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-cog"></i>
                                Additional Settings
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sort Order</label>
                                        <input type="number" 
                                               class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" 
                                               name="sort_order" 
                                               value="{{ old('sort_order', $bundle->sort_order) }}" 
                                               min="0">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" 
                                                name="status">
                                            <option value="1" {{ old('status', $bundle->status) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $bundle->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Summary & Actions -->
                <div class="col-lg-4">
                    <!-- Bundle Summary -->
                    <div class="modern-card mb-4">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-info-circle"></i>
                                Bundle Summary
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <div class="bundle-summary-item">
                                <strong>Selected Products:</strong>
                                <span id="summaryProductCount" class="badge bg-primary">{{ $bundle->products->count() }}</span>
                            </div>
                            <div class="bundle-summary-item">
                                <strong>Total Products Price:</strong>
                                <span id="summaryTotalPrice">$0.00</span>
                            </div>
                            <div class="bundle-summary-item">
                                <strong>Bundle Price:</strong>
                                <span id="summaryBundlePrice">${{ number_format($bundle->bundle_price, 2) }}</span>
                            </div>
                            <div class="bundle-summary-item">
                                <strong>Savings:</strong>
                                <span id="summarySavings" class="text-success">$0.00</span>
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
                                    <i class="fas fa-save me-2"></i>Update Bundle
                                </button>
                                <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Help Tips -->
                    <div class="modern-card">
                        <div class="modern-card__header">
                            <h3 class="modern-card__title">
                                <i class="fas fa-lightbulb"></i>
                                Tips
                            </h3>
                        </div>
                        <div class="modern-card__body">
                            <ul class="tips-list">
                                <li>Select at least 2 products to create a bundle</li>
                                <li>Use category filter to narrow down products</li>
                                <li>Type product name to search quickly</li>
                                <li>Set bundle price lower than total for discount</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Wait for jQuery and Select2 to be loaded
(function() {
    function initSelect2() {
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
            initializeBundleForm();
        } else {
            setTimeout(initSelect2, 100);
        }
    }

    function initializeBundleForm() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initBundleForm();
            });
        } else {
            initBundleForm();
        }
    }

    function initBundleForm() {
        let selectedProducts = [];
        let productQuantities = {};

        // Initialize selected products from bundle
        @foreach($bundle->products as $product)
        selectedProducts.push({
            id: {{ $product->id }},
            name: '{{ addslashes($product->name) }}',
            price: {{ $product->total_price }},
            quantity: {{ $product->pivot->quantity ?? 1 }}
        });
        productQuantities[{{ $product->id }}] = {{ $product->pivot->quantity ?? 1 }};
        @endforeach

        // Initialize Select2
        $('#product_ids').select2({
        theme: 'bootstrap-5',
        placeholder: 'Search and select products...',
        allowClear: true,
        ajax: {
            url: '{{ route("admin.bundles.searchProducts") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    category_id: $('#category_filter').val(),
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        minimumInputLength: 0,
        templateResult: formatProduct,
        templateSelection: formatProductSelection
        });

        // Category filter change
        $('#category_filter').on('change', function() {
        $('#product_ids').val(selectedProducts.map(p => p.id)).trigger('change');
        });

        // Product selection change
        $('#product_ids').on('select2:select', function (e) {
        const data = e.params.data;
        if (!selectedProducts.find(p => p.id == data.id)) {
            selectedProducts.push({
                id: data.id,
                name: data.name,
                price: parseFloat(data.price) || 0,
                quantity: 1
            });
            productQuantities[data.id] = 1;
        }
        updateSelectedProductsList();
        updateSummary();
        });

        $('#product_ids').on('select2:unselect', function (e) {
        const data = e.params.data;
        selectedProducts = selectedProducts.filter(p => p.id != data.id);
        delete productQuantities[data.id];
        updateSelectedProductsList();
        updateSummary();
        });

        // Format product display in dropdown
        function formatProduct(product) {
        if (product.loading) {
            return 'Searching...';
        }
        return $('<div class="product-select-item">' +
            '<div class="product-select-name">' + product.text + '</div>' +
            '</div>');
        }

        function formatProductSelection(product) {
        return product.text || product.name;
        }

        // Update selected products list
        function updateSelectedProductsList() {
            const container = $('#selectedProductsContainer');
            if (selectedProducts.length === 0) {
                container.html('<p class="text-muted">No products selected yet</p>');
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th>Action</th></tr></thead>';
            html += '<tbody>';

            selectedProducts.forEach(product => {
                const quantity = productQuantities[product.id] || 1;
                const total = product.price * quantity;
                html += `<tr data-product-id="${product.id}">
                    <td>${product.name}</td>
                    <td>$${product.price.toFixed(2)}</td>
                    <td>
                        <input type="number" 
                               class="form-control form-control-sm quantity-input" 
                               value="${quantity}" 
                               min="1" 
                               data-product-id="${product.id}"
                               style="width: 70px;">
                    </td>
                    <td>$${total.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-product" data-product-id="${product.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>`;
            });

            html += '</tbody></table></div>';
            container.html(html);

            // Quantity change handler
            $('.quantity-input').on('change', function() {
                const productId = $(this).data('product-id');
                const quantity = parseInt($(this).val()) || 1;
                productQuantities[productId] = quantity;
                const product = selectedProducts.find(p => p.id == productId);
                if (product) {
                    product.quantity = quantity;
                }
                updateSummary();
                updateSelectedProductsList();
            });

            // Remove product handler
            $('.remove-product').on('click', function() {
                const productId = $(this).data('product-id');
                $('#product_ids').val(selectedProducts.filter(p => p.id != productId).map(p => p.id)).trigger('change');
            });
        }

        // Update summary
        function updateSummary() {
            const productCount = selectedProducts.length;
            let totalPrice = 0;
            
            selectedProducts.forEach(product => {
                const quantity = productQuantities[product.id] || 1;
                totalPrice += product.price * quantity;
            });

            const bundlePrice = parseFloat($('#bundle_price').val()) || 0;
            const savings = totalPrice - bundlePrice;

            // Calculate discount percentage
            let discountPercentage = 0;
            if (totalPrice > 0 && bundlePrice > 0) {
                discountPercentage = ((totalPrice - bundlePrice) / totalPrice) * 100;
                discountPercentage = Math.max(0, Math.min(100, discountPercentage)); // Clamp between 0-100
            }

            // Update summary sidebar
            $('#summaryProductCount').text(productCount);
            $('#summaryTotalPrice').text('$' + totalPrice.toFixed(2));
            $('#summaryBundlePrice').text('$' + bundlePrice.toFixed(2));
            $('#summarySavings').text('$' + (savings > 0 ? savings.toFixed(2) : '0.00'));

            if (savings > 0) {
                $('#summarySavings').removeClass('text-danger').addClass('text-success');
            } else {
                $('#summarySavings').removeClass('text-success').addClass('text-danger');
            }

            // Update pricing section
            $('#totalProductsValue').text('$' + totalPrice.toFixed(2));
            $('#displayBundlePrice').text('$' + bundlePrice.toFixed(2));
            $('#customerSavings').text('$' + (savings > 0 ? savings.toFixed(2) : '0.00'));

            // Update discount percentage (both visible and hidden)
            $('#discount_percentage').val(discountPercentage.toFixed(2) + '%');
            $('#discount_percentage_hidden').val(discountPercentage.toFixed(2));
        }

        // Bundle price change
        $('#bundle_price').on('input', updateSummary);
        
        // Initial summary update on page load
        setTimeout(function() {
            updateSummary();
        }, 500);

        // Form submission - prepare product_ids and quantities arrays
        $('#bundleForm').on('submit', function(e) {
            if (selectedProducts.length < 2) {
                e.preventDefault();
                alert('Please select at least 2 products');
                return false;
            }

            // Create hidden inputs for product_ids and quantities
            selectedProducts.forEach((product, index) => {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'product_ids[]',
                    value: product.id
                }).appendTo(this);

                $('<input>').attr({
                    type: 'hidden',
                    name: 'quantities[]',
                    value: productQuantities[product.id] || 1
                }).appendTo(this);
            });
        });

        // Image preview
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + previewId + ' img').attr('src', e.target.result);
                    $('#' + previewId).show();
                    $('#currentImagePreview').hide();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Initialize on page load
        updateSelectedProductsList();
        updateSummary();
    }

    // Start initialization
    initSelect2();
})();
</script>
@endpush

<style>
.selected-products-list {
    margin-top: 1rem;
}

.selected-products-container {
    max-height: 400px;
    overflow-y: auto;
}

.product-select-item {
    padding: 5px;
}

.product-select-name {
    font-weight: 500;
}

.bundle-summary-item {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.bundle-summary-item:last-child {
    border-bottom: none;
}

.tips-list {
    margin: 0;
    padding-left: 1.5rem;
}

.tips-list li {
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}
</style>
@endsection
