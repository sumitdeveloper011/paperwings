@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Product Bundle
                </h1>
                <p class="page-header__subtitle">Create a new product bundle</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.bundles.store') }}" enctype="multipart/form-data" id="bundleForm" class="modern-form">
                @csrf
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
                                       value="{{ old('name') }}"
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
                                          placeholder="Enter bundle description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bundle Images -->
                    @include('components.multiple-image-upload', [
                        'name' => 'images',
                        'id' => 'images',
                        'label' => 'Upload Images',
                        'existingImages' => null,
                        'entityName' => 'Bundle',
                        'showKeepExisting' => false,
                        'maxImages' => 10
                    ])

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
                                @include('components.select-category', [
                                    'id' => 'category_filter',
                                    'name' => 'category_filter',
                                    'label' => 'Filter by Category (Optional)',
                                    'required' => false,
                                    'selected' => old('category_filter'),
                                    'categories' => $categories,
                                    'useUuid' => false,
                                    'placeholder' => 'All Categories',
                                    'class' => 'form-select',
                                    'useSelect2' => true,
                                    'showLabel' => true,
                                    'wrapperClass' => '',
                                    'select2Width' => '100%'
                                ])
                                <small class="form-text text-muted">Select a category to filter products</small>
                            </div>

                            <div class="mb-3">
                                <label for="product_ids" class="form-label">Select Products <span class="text-danger">*</span></label>
                                <select class="form-select" id="product_ids" name="product_ids_select" multiple>
                                    @php
                                        // Load old products if validation failed
                                        $oldProductIds = old('product_ids', []);
                                        $oldQuantities = old('quantities', []);
                                        $oldProducts = collect();
                                        if (!empty($oldProductIds)) {
                                            $productsById = \App\Models\Product::whereIn('id', $oldProductIds)->get()->keyBy('id');
                                            $oldProducts = collect($oldProductIds)->map(function($productId, $index) use ($productsById, $oldQuantities) {
                                                if (!$productsById->has($productId)) return null;
                                                $product = $productsById->get($productId);
                                                $quantity = isset($oldQuantities[$index]) ? $oldQuantities[$index] : 1;
                                                $product->setRelation('pivot', (object)['quantity' => $quantity]);
                                                return $product;
                                            })->filter();
                                        }
                                    @endphp
                                    @if(!empty($oldProductIds) && $oldProducts->count() > 0)
                                        @foreach($oldProducts as $product)
                                            @php
                                                $productIndex = array_search($product->id, $oldProductIds);
                                                $quantity = ($productIndex !== false && isset($oldQuantities[$productIndex])) ? $oldQuantities[$productIndex] : 1;
                                            @endphp
                                            <option value="{{ $product->id }}" selected data-price="{{ $product->total_price }}" data-name="{{ $product->name }}" data-quantity="{{ $quantity }}">
                                                {{ $product->name }} - ${{ number_format($product->total_price, 2) }}
                                            </option>
                                        @endforeach
                                    @endif
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
                                    <p class="text-muted">No products selected yet</p>
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
                                               value="{{ old('bundle_price') }}"
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
                                               value="{{ old('discount_percentage') }}"
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
                                               value="{{ old('sort_order', 0) }}"
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
                                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i>
                            Create Bundle
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg" onclick="resetForm()" style="background-color: #f8f9fa;">
                            <i class="fas fa-undo"></i>
                            Reset Form
                        </button>
                        <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-secondary btn-lg" style="background-color: #f8f9fa;">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Tips Section - At Top -->
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
                                    <strong>Product Selection</strong>
                                    <p>Select at least 2 products to create a bundle</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Category Filter</strong>
                                    <p>Use category filter to narrow down products</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Search Products</strong>
                                    <p>Type product name to search quickly</p>
                                </div>
                            </li>
                            <li class="tips-list__item">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Bundle Pricing</strong>
                                    <p>Set bundle price lower than total for discount</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Bundle Summary -->
                <div class="modern-card">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Bundle Summary
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-item__label">
                                    <i class="fas fa-box"></i>
                                    Selected Products
                                </div>
                                <div class="detail-item__value">
                                    <span id="summaryProductCount" class="badge badge--info">0</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-item__label">
                                    <i class="fas fa-dollar-sign"></i>
                                    Total Products Price
                                </div>
                                <div class="detail-item__value">
                                    <span id="summaryTotalPrice" class="text-primary">$0.00</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-item__label">
                                    <i class="fas fa-tag"></i>
                                    Bundle Price
                                </div>
                                <div class="detail-item__value">
                                    <span id="summaryBundlePrice" class="text-success">$0.00</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-item__label">
                                    <i class="fas fa-percent"></i>
                                    Savings
                                </div>
                                <div class="detail-item__value">
                                    <span id="summarySavings" class="text-success">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
// Wait for jQuery and Select2 to be fully loaded
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
        // Check if element exists
        if ($('#product_ids').length === 0) {
            console.error('Product select element not found');
            return;
        }

        let selectedProducts = [];
        let productQuantities = {};

        // Initialize selected products from old input if validation failed
        @if(!empty($oldProductIds) && $oldProducts->count() > 0)
            @foreach($oldProducts as $product)
                @php
                    $productIndex = array_search($product->id, $oldProductIds);
                    $quantity = ($productIndex !== false && isset($oldQuantities[$productIndex])) ? $oldQuantities[$productIndex] : 1;
                @endphp
                selectedProducts.push({
                    id: {{ $product->id }},
                    name: '{{ addslashes($product->name) }}',
                    price: {{ $product->total_price }},
                    quantity: {{ $quantity }}
                });
                productQuantities[{{ $product->id }}] = {{ $quantity }};
            @endforeach
        @endif

        // Format product display in dropdown (must be defined before Select2 initialization)
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

        // Destroy existing Select2 instance if any
        if ($('#product_ids').data('select2')) {
            $('#product_ids').select2('destroy');
        }

        // Initialize Select2
        $('#product_ids').select2({
            theme: 'bootstrap-5',
            placeholder: 'Click to search and select products...',
            allowClear: true,
            ajax: {
                url: '{{ route("admin.bundles.searchProducts") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || '',
                        category_id: $('#category_filter').val() || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    // Ensure all custom properties are preserved
                    const results = (data.results || []).map(function(item) {
                        return {
                            id: item.id,
                            text: item.text,
                            name: item.name,
                            price: item.price,
                            image: item.image
                        };
                    });
                    return {
                        results: results,
                        pagination: {
                            more: (data.pagination && data.pagination.more) || false
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: formatProduct,
            templateSelection: formatProductSelection
        });

        // After Select2 initialization, ensure selected products are synced
        setTimeout(function() {
            if (selectedProducts.length > 0) {
                const selectedIds = selectedProducts.map(p => p.id);

                // Create options for already selected products and add to Select2
                selectedProducts.forEach(function(product) {
                    // Check if option already exists
                    if ($('#product_ids option[value="' + product.id + '"]').length === 0) {
                        const option = new Option(
                            product.name + ' - $' + product.price.toFixed(2),
                            product.id,
                            true,
                            true
                        );
                        option.setAttribute('data-price', product.price);
                        option.setAttribute('data-name', product.name);
                        $('#product_ids').append(option);
                    }
                });

                // Set values and trigger change
                $('#product_ids').val(selectedIds).trigger('change');
            }
            updateSelectedProductsList();
        }, 500);

        // After Select2 initialization, ensure selected products are synced
        setTimeout(function() {
            if (selectedProducts.length > 0) {
                const selectedIds = selectedProducts.map(p => p.id);

                // Create options for already selected products and add to Select2
                selectedProducts.forEach(function(product) {
                    // Check if option already exists
                    if ($('#product_ids option[value="' + product.id + '"]').length === 0) {
                        const option = new Option(
                            product.name + ' - $' + product.price.toFixed(2),
                            product.id,
                            true,
                            true
                        );
                        option.setAttribute('data-price', product.price);
                        option.setAttribute('data-name', product.name);
                        $('#product_ids').append(option);
                    }
                });

                // Set values and trigger change
                $('#product_ids').val(selectedIds).trigger('change');
            }
            updateSelectedProductsList();
        }, 500);

        // Category filter change - reload products when category changes
        $(document).on('change', '#category_filter', function() {
            // Keep currently selected products
            const currentSelected = selectedProducts.map(p => p.id);
            // The Select2 AJAX will automatically filter by category_id
            // No need to clear, just let Select2 reload
        });

        // Product selection change
        $('#product_ids').on('select2:select', function (e) {
            const data = e.params.data;
            const productId = parseInt(data.id);

            // Check if product already exists
            if (selectedProducts.find(p => p.id == productId)) {
                return;
            }

            // Extract product name and price from the text or data
            let productName = data.text || data.name || '';
            let productPrice = 0;

            // Try to extract price from text if available
            const priceMatch = productName.match(/\$([\d.]+)/);
            if (priceMatch) {
                productPrice = parseFloat(priceMatch[1]) || 0;
                // Remove price from name
                productName = productName.replace(/\s*-\s*\$[\d.]+/, '').trim();
            }

            // If price is in data object, use it
            if (data.price) {
                productPrice = parseFloat(data.price) || 0;
            }

            // If name is in data object, use it
            if (data.name) {
                productName = data.name;
            }

            // Add to selected products
            selectedProducts.push({
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1
            });
            productQuantities[productId] = 1;

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
            // Store clean numeric value without % for form submission
            $('#discount_percentage_hidden').val(discountPercentage.toFixed(2));
        }

        // Bundle price change handler
        $('#bundle_price').on('input change', function() {
            updateSummary();
        });

        // Update bundle price from old input if validation failed
        @if(old('bundle_price'))
            $(document).ready(function() {
                $('#bundle_price').val({{ old('bundle_price') }});
                updateSummary();
            });
        @endif

        // Initial summary update on page load - ensure it runs after everything is initialized
        setTimeout(function() {
            updateSummary();
            updateSelectedProductsList();
        }, 800);

        // Form submission - prepare product_ids and quantities arrays
        $('#bundleForm').on('submit', function(e) {
            if (selectedProducts.length < 2) {
                e.preventDefault();
                alert('Please select at least 2 products');
                return false;
            }

            // Remove existing hidden inputs if any
            $('input[name="product_ids[]"]').not('#product_ids').remove();
            $('input[name="quantities[]"]').remove();

            // Clear the Select2 select to avoid conflicts
            $('#product_ids').val(null).trigger('change');

            // Clean discount percentage - remove % sign if present
            const discountValue = $('#discount_percentage_hidden').val();
            if (discountValue) {
                const cleanDiscount = discountValue.toString().replace('%', '').trim();
                $('#discount_percentage_hidden').val(cleanDiscount);
            }

            // Remove any duplicate product IDs before submission
            const uniqueProducts = [];
            const seenIds = new Set();

            selectedProducts.forEach((product) => {
                if (!seenIds.has(product.id)) {
                    seenIds.add(product.id);
                    uniqueProducts.push(product);
                }
            });

            // Create hidden inputs for product_ids and quantities (only unique products)
            uniqueProducts.forEach((product, index) => {
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

        // Reset form
        function resetForm() {
        if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
            $('#bundleForm')[0].reset();
            $('#product_ids').val(null).trigger('change');
            selectedProducts = [];
            productQuantities = {};
            updateSelectedProductsList();
            updateSummary();
            $('#imagesPreviewContainer').html('');
        }
    }

        // Make functions available globally
        window.resetForm = resetForm;
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

.tips-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.tips-list__item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}

.tips-list__item:last-child {
    border-bottom: none;
}

.tips-list__item i {
    color: var(--success-color);
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.tips-list__item strong {
    display: block;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.tips-list__item p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.875rem;
}
</style>
@endsection
