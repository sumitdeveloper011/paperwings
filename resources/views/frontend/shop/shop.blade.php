@extends('layouts.frontend.main')
@section('content')
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Shop</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">Shop</h1>
                    <p class="page-subtitle">Discover our amazing collection of products</p>
                </div>
            </div>
        </div>
    </section>

    <section class="category-products-section">
        <div class="container">
            <div class="row">
                <!-- Filters Sidebar -->
                <div class="col-lg-3 col-md-4">
                    <div class="filters-sidebar">
                        <div class="filters-header">
                            <h3>Filters</h3>
                            <button class="btn btn-sm btn-link clear-filters" id="clearFilters">Clear All</button>
                        </div>

                        <!-- Active Filters -->
                        <div class="active-filters mb-3" id="activeFilters" style="display: none;">
                            <h5>Active Filters:</h5>
                            <div class="filter-chips" id="filterChips"></div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="filter-group mb-4">
                            <h5>PRICE RANGE</h5>
                            <div class="price-range-wrapper">
                                <div class="price-inputs">
                                    <input type="number" class="price-input" id="minPrice" placeholder="Min" value="{{ $minPrice ?? $priceMin }}" min="{{ $priceMin }}" max="{{ $priceMax }}">
                                    <input type="number" class="price-input" id="maxPrice" placeholder="Max" value="{{ $maxPrice ?? $priceMax }}" min="{{ $priceMin }}" max="{{ $priceMax }}">
                                </div>
                                <div class="price-slider-container">
                                    <div class="price-slider-track"></div>
                                    <input type="range" class="price-slider-input price-slider-min" id="priceSliderMin" min="{{ $priceMin }}" max="{{ $priceMax }}" value="{{ $minPrice ?? $priceMin }}">
                                    <input type="range" class="price-slider-input price-slider-max" id="priceSliderMax" min="{{ $priceMin }}" max="{{ $priceMax }}" value="{{ $maxPrice ?? $priceMax }}">
                                    <div class="price-display-overlay">
                                        <span id="priceDisplay">${{ $minPrice ?? $priceMin }} - ${{ $maxPrice ?? $priceMax }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-group mb-4">
                            <h5>Categories</h5>
                            <div class="filter-checkboxes">
                                @foreach($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input category-filter" type="checkbox" value="{{ $category->id }}" id="category{{ $category->id }}" 
                                            {{ in_array($category->id, $categoriesFilter ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category{{ $category->id }}">
                                            {{ $category->name }} ({{ $category->products_count }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Brand Filter -->
                        @if(isset($brands) && $brands->count() > 0)
                        <div class="filter-group mb-4">
                            <h5>Brands</h5>
                            <div class="filter-checkboxes">
                                @foreach($brands as $brand)
                                    <div class="form-check">
                                        <input class="form-check-input brand-filter" type="checkbox" value="{{ $brand->id }}" id="brand{{ $brand->id }}"
                                            {{ in_array($brand->id, $brandsFilter ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="brand{{ $brand->id }}">
                                            {{ $brand->name }} ({{ $brand->active_products_count }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif


                        <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8">
                    <div class="products-header">
                        <div class="products-header__left">
                            <p class="products-count">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
                        </div>
                        <div class="products-header__right">
                            <div class="sort-dropdown">
                                <select class="sort-select" id="sortSelect">
                                    <option value="featured" {{ (isset($sort) && $sort == 'featured') ? 'selected' : '' }}>Sort by: Featured</option>
                                    <option value="price_low_high" {{ (isset($sort) && $sort == 'price_low_high') ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high_low" {{ (isset($sort) && $sort == 'price_high_low') ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="name_asc" {{ (isset($sort) && $sort == 'name_asc') ? 'selected' : '' }}>Name: A to Z</option>
                                    <option value="name_desc" {{ (isset($sort) && $sort == 'name_desc') ? 'selected' : '' }}>Name: Z to A</option>
                                    <option value="newest" {{ (isset($sort) && $sort == 'newest') ? 'selected' : '' }}>Newest First</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="products-grid" id="productsGrid">
                        @if($products && $products->count() > 0)
                        @foreach($products as $product)
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <a href="{{ route('product.detail', $product->slug) }}" class="cute-stationery__image-link">
                                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="cute-stationery__img">
                                </a>
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $product->id }}" title="Add to Wishlist"><i class="far fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart add-to-cart" data-product-id="{{ $product->id }}" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">
                                    <a href="{{ route('product.detail', $product->slug) }}" class="cute-stationery__name-link">
                                    {{ $product->name }}
                                    </a>
                                </h3>
                                <div class="cute-stationery__price">
                                    @if($product->discount_price)
                                    <span class="cute-stationery__price-current">${{ number_format($product->discount_price, 2) }}</span>
                                    <span class="cute-stationery__price-old">${{ number_format($product->total_price, 2) }}</span>
                                    @else
                                    <span class="cute-stationery__price-current">${{ number_format($product->total_price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="products-grid__empty">
                            <div class="empty-state">
                                <i class="fas fa-shopping-bag empty-state__icon"></i>
                                <p class="empty-state__text">No products found. Try adjusting your filters.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @if($products && $products->hasPages())
                        @include('include.frontend.pagination', ['paginator' => $products])
                    @endif
                </div>
            </div>
        </div>
    </section>

@push('styles')
<style>
.price-range-wrapper {
    position: relative;
}

.price-inputs {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.price-input {
    flex: 1;
    padding: 0.625rem 0.875rem;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 0.95rem;
    font-weight: 500;
    color: #2c3e50;
    background: #fff;
    transition: all 0.2s ease;
    -moz-appearance: textfield;
}

.price-input::-webkit-outer-spin-button,
.price-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.price-input:focus {
    outline: none;
    border-color: var(--coral-red);
    box-shadow: 0 0 0 3px rgba(233, 92, 103, 0.1);
}

.price-slider-container {
    position: relative;
    height: 40px;
    margin: 1.5rem 0;
}

.price-slider-track {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    transform: translateY(-50%);
    z-index: 1;
}

.price-slider-input {
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 6px;
    margin: 0;
    padding: 0;
    background: transparent;
    outline: none;
    -webkit-appearance: none;
    appearance: none;
    z-index: 2;
    transform: translateY(-50%);
    pointer-events: none;
}

.price-slider-input::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: var(--coral-red);
    border: 3px solid #fff;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(233, 92, 103, 0.3);
    transition: all 0.2s ease;
    pointer-events: all;
}

.price-slider-input::-webkit-slider-thumb:hover {
    transform: scale(1.1);
    box-shadow: 0 3px 8px rgba(233, 92, 103, 0.4);
}

.price-slider-input::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: var(--coral-red);
    border: 3px solid #fff;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(233, 92, 103, 0.3);
    transition: all 0.2s ease;
    pointer-events: all;
    -moz-appearance: none;
}

.price-slider-input::-moz-range-thumb:hover {
    transform: scale(1.1);
    box-shadow: 0 3px 8px rgba(233, 92, 103, 0.4);
}

.price-slider-input::-moz-range-track {
    background: transparent;
    height: 6px;
}

.price-slider-min {
    z-index: 3;
}

.price-slider-max {
    z-index: 2;
}

.price-display-overlay {
    position: absolute;
    top: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--coral-red);
    color: #fff;
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 700;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(233, 92, 103, 0.3);
    z-index: 10;
}

.price-display-overlay::after {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid var(--coral-red);
}

.price-slider-input:active + .price-slider-input + .price-display-overlay,
.price-slider-input:focus + .price-slider-input + .price-display-overlay {
    transform: translateX(-50%) scale(1.05);
}

@media (max-width: 768px) {
    .price-inputs {
        gap: 0.5rem;
    }
    
    .price-display-overlay {
        font-size: 0.8rem;
        padding: 0.3rem 0.7rem;
        top: -32px;
    }
}
</style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('sortSelect');
            const applyFiltersBtn = document.getElementById('applyFilters');
            const clearFiltersBtn = document.getElementById('clearFilters');

            // Sort functionality
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    updateUrl();
                });
            }

            // Apply Filters
            if (applyFiltersBtn) {
                applyFiltersBtn.addEventListener('click', function() {
                    updateUrl();
                });
            }

            // Clear Filters
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Uncheck all checkboxes
                    document.querySelectorAll('.category-filter, .brand-filter').forEach(cb => cb.checked = false);
                    
                    // Reset price
                    document.getElementById('minPrice').value = '{{ $priceMin }}';
                    document.getElementById('maxPrice').value = '{{ $priceMax }}';
                    document.getElementById('priceSliderMin').value = '{{ $priceMin }}';
                    document.getElementById('priceSliderMax').value = '{{ $priceMax }}';
                    updatePriceDisplay();
                    
                    // Redirect to clean URL
                    window.location.href = '{{ route("shop") }}';
                });
            }

            // Price Slider - Dual Range
            const priceSliderMin = document.getElementById('priceSliderMin');
            const priceSliderMax = document.getElementById('priceSliderMax');
            const minPriceInput = document.getElementById('minPrice');
            const maxPriceInput = document.getElementById('maxPrice');
            const priceDisplay = document.getElementById('priceDisplay');
            const priceSliderTrack = document.querySelector('.price-slider-track');

            function updatePriceDisplay() {
                const min = minPriceInput.value || '{{ $priceMin }}';
                const max = maxPriceInput.value || '{{ $priceMax }}';
                if (priceDisplay) {
                    priceDisplay.textContent = `$${min} - $${max}`;
                }
                updateSliderTrack();
            }

            function updateSliderTrack() {
                if (!priceSliderMin || !priceSliderMax || !priceSliderTrack) return;
                
                const min = parseFloat(priceSliderMin.value);
                const max = parseFloat(priceSliderMax.value);
                const minVal = parseFloat(priceSliderMin.min);
                const maxVal = parseFloat(priceSliderMax.max);
                
                const minPercent = ((min - minVal) / (maxVal - minVal)) * 100;
                const maxPercent = ((max - minVal) / (maxVal - minVal)) * 100;
                
                priceSliderTrack.style.background = `linear-gradient(to right, 
                    #e9ecef 0%, 
                    #e9ecef ${minPercent}%, 
                    var(--coral-red) ${minPercent}%, 
                    var(--coral-red) ${maxPercent}%, 
                    #e9ecef ${maxPercent}%, 
                    #e9ecef 100%)`;
            }

            if (priceSliderMin && priceSliderMax) {
                priceSliderMin.addEventListener('input', function() {
                    const minVal = parseFloat(this.value);
                    const maxVal = parseFloat(priceSliderMax.value);
                    
                    if (minVal > maxVal) {
                        this.value = maxVal;
                    }
                    
                    minPriceInput.value = this.value;
                    updatePriceDisplay();
                });

                priceSliderMax.addEventListener('input', function() {
                    const minVal = parseFloat(priceSliderMin.value);
                    const maxVal = parseFloat(this.value);
                    
                    if (maxVal < minVal) {
                        this.value = minVal;
                    }
                    
                    maxPriceInput.value = this.value;
                    updatePriceDisplay();
                });
            }

            if (minPriceInput && maxPriceInput) {
                minPriceInput.addEventListener('input', function() {
                    const minVal = parseFloat(this.value) || parseFloat(this.min);
                    const maxVal = parseFloat(maxPriceInput.value) || parseFloat(maxPriceInput.max);
                    
                    if (minVal > maxVal) {
                        this.value = maxVal;
                    }
                    
                    if (priceSliderMin) {
                        priceSliderMin.value = this.value;
                    }
                    updatePriceDisplay();
                });

                maxPriceInput.addEventListener('input', function() {
                    const minVal = parseFloat(minPriceInput.value) || parseFloat(minPriceInput.min);
                    const maxVal = parseFloat(this.value) || parseFloat(this.max);
                    
                    if (maxVal < minVal) {
                        this.value = minVal;
                    }
                    
                    if (priceSliderMax) {
                        priceSliderMax.value = this.value;
                    }
                    updatePriceDisplay();
                });
            }

            // Initialize slider track
            updatePriceDisplay();

            function updateUrl() {
                const currentUrl = new URL(window.location.href);
                
                // Get selected categories
                const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(cb => cb.value);
                if (selectedCategories.length > 0) {
                    currentUrl.searchParams.delete('category');
                    currentUrl.searchParams.delete('categories');
                    selectedCategories.forEach(id => {
                        currentUrl.searchParams.append('categories[]', id);
                    });
                } else {
                    currentUrl.searchParams.delete('categories');
                }

                // Get selected brands
                const selectedBrands = Array.from(document.querySelectorAll('.brand-filter:checked')).map(cb => cb.value);
                if (selectedBrands.length > 0) {
                    currentUrl.searchParams.delete('brands');
                    selectedBrands.forEach(id => {
                        currentUrl.searchParams.append('brands[]', id);
                    });
                } else {
                    currentUrl.searchParams.delete('brands');
                }


                // Get price range
                const minPrice = minPriceInput.value;
                const maxPrice = maxPriceInput.value;
                if (minPrice && minPrice != '{{ $priceMin }}') {
                    currentUrl.searchParams.set('min_price', minPrice);
                } else {
                    currentUrl.searchParams.delete('min_price');
                }
                if (maxPrice && maxPrice != '{{ $priceMax }}') {
                    currentUrl.searchParams.set('max_price', maxPrice);
                } else {
                    currentUrl.searchParams.delete('max_price');
                }

                // Get sort
                if (sortSelect && sortSelect.value) {
                    currentUrl.searchParams.set('sort', sortSelect.value);
                }

                // Reset to page 1
                currentUrl.searchParams.delete('page');

                // Update active filters display
                updateActiveFilters();

                // Redirect
                window.location.href = currentUrl.toString();
            }

            function updateActiveFilters() {
                const activeFilters = [];
                const filterChips = document.getElementById('filterChips');
                const activeFiltersDiv = document.getElementById('activeFilters');

                // Categories
                document.querySelectorAll('.category-filter:checked').forEach(cb => {
                    activeFilters.push({
                        type: 'category',
                        value: cb.value,
                        label: cb.nextElementSibling.textContent.trim()
                    });
                });

                // Brands
                document.querySelectorAll('.brand-filter:checked').forEach(cb => {
                    activeFilters.push({
                        type: 'brand',
                        value: cb.value,
                        label: cb.nextElementSibling.textContent.trim()
                    });
                });


                // Price
                const minPrice = minPriceInput.value;
                const maxPrice = maxPriceInput.value;
                if (minPrice && minPrice != '{{ $priceMin }}' || maxPrice && maxPrice != '{{ $priceMax }}') {
                    activeFilters.push({
                        type: 'price',
                        value: `${minPrice}-${maxPrice}`,
                        label: `Price: $${minPrice} - $${maxPrice}`
                    });
                }

                if (activeFilters.length > 0) {
                    filterChips.innerHTML = activeFilters.map(filter => 
                        `<span class="badge bg-primary me-2 mb-2">${filter.label} <button type="button" class="btn-close btn-close-white ms-1" onclick="removeFilter('${filter.type}', '${filter.value}')"></button></span>`
                    ).join('');
                    activeFiltersDiv.style.display = 'block';
                } else {
                    activeFiltersDiv.style.display = 'none';
                }
            }

            // Initialize active filters display
            updateActiveFilters();
        });

        function removeFilter(type, value) {
            if (type === 'category') {
                document.getElementById('category' + value).checked = false;
            } else if (type === 'brand') {
                document.getElementById('brand' + value).checked = false;
            } else if (type === 'price') {
                document.getElementById('minPrice').value = '{{ $priceMin }}';
                document.getElementById('maxPrice').value = '{{ $priceMax }}';
            }
            updateUrl();
        }
    </script>
@endpush
@endsection

