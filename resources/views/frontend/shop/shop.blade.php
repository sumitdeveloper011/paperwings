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
                    <div class="category-sidebar">
                        <!-- Categories Widget -->
                        <div class="sidebar-widget">
                            <div class="sidebar-widget__header">
                                <h3 class="sidebar-widget__title">
                                    <i class="fas fa-th-large sidebar-widget__icon"></i>
                                    Categories
                                </h3>
                            </div>
                            <div class="sidebar-widget__body">
                                <!-- Category Search Box (only show if more than 10 categories) -->
                                @if($categories && $categories->count() > 10)
                                <div class="category-search-box mb-3">
                                    <input type="text" class="category-search-input" id="categorySearch" placeholder="Search categories...">
                                    <i class="fas fa-search category-search-icon"></i>
                                </div>
                                @endif

                                <!-- Categories List Container -->
                                <div class="categories-list-container" id="categoriesListContainer">
                                    <ul class="sidebar-categories" id="categoriesList">
                                        @if($categories && $categories->count() > 0)
                                        @foreach($categories as $index => $category)
                                        <li class="sidebar-category {{ $index >= 10 ? 'category-item-hidden' : '' }}"
                                            data-category-name="{{ strtolower($category->name) }}"
                                            data-category-index="{{ $index }}">
                                            <label class="sidebar-category__link sidebar-category__link--checkbox">
                                                <input class="category-filter" type="checkbox" value="{{ $category->id }}" id="category{{ $category->id }}"
                                                    {{ in_array($category->id, $categoriesFilter ?? []) ? 'checked' : '' }}>
                                                <span class="sidebar-category__name">{{ $category->name }} <span class="category-count">({{ $category->active_products_count ?? 0 }})</span></span>
                                            </label>
                                        </li>
                                        @endforeach
                                        @else
                                        <li class="sidebar-category--empty">
                                            <span class="sidebar-category__empty-text">No categories available</span>
                                        </li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Load More Button (only show if more than 10 categories) -->
                                @if($categories && $categories->count() > 10)
                                <div class="text-center mt-3">
                                    <button type="button" class="btn-load-more-categories" id="loadMoreCategories" data-items-per-page="10">
                                        <span class="load-more-text">Load More</span>
                                        <span class="load-all-text" style="display: none;">Show All</span>
                                        <i class="fas fa-chevron-down ms-1"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Price Range Widget -->
                        <div class="sidebar-widget">
                            <div class="sidebar-widget__header">
                                <h3 class="sidebar-widget__title">
                                    <i class="fas fa-dollar-sign sidebar-widget__icon"></i>
                                    Price Range
                                </h3>
                            </div>
                            <div class="sidebar-widget__body">
                                <div class="price-filter">
                                    <div class="price-range-display">
                                        <div class="price-range-display__item">
                                            <span class="price-range-display__label">Min</span>
                                            <span class="price-range-display__value">$<span id="priceMinDisplay">{{ $minPrice ?? $priceMin }}</span></span>
                                        </div>
                                        <div class="price-range-display__divider">-</div>
                                        <div class="price-range-display__item">
                                            <span class="price-range-display__label">Max</span>
                                            <span class="price-range-display__value">$<span id="priceMaxDisplay">{{ $maxPrice ?? $priceMax }}</span></span>
                                        </div>
                                    </div>
                                    <div class="price-range-slider">
                                        <input type="range" class="price-range" id="priceRange"
                                               min="{{ $priceMin }}"
                                               max="{{ $priceMax }}"
                                               value="{{ $maxPrice ?? $priceMax }}"
                                               step="1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Brands Widget -->
                        @if(isset($brands) && $brands->count() > 0)
                        <div class="sidebar-widget">
                            <div class="sidebar-widget__header">
                                <h3 class="sidebar-widget__title">
                                    <i class="fas fa-tags sidebar-widget__icon"></i>
                                    Brands
                                </h3>
                            </div>
                            <div class="sidebar-widget__body">
                                <ul class="sidebar-categories">
                                    @foreach($brands as $brand)
                                    <li class="sidebar-category">
                                        <label class="sidebar-category__link" style="cursor: pointer; margin: 0;">
                                            <input class="brand-filter" type="checkbox" value="{{ $brand->id }}" id="brand{{ $brand->id }}"
                                                {{ in_array($brand->id, $brandsFilter ?? []) ? 'checked' : '' }}
                                                style="position: absolute; opacity: 0; width: 0; height: 0;">
                                            <span class="sidebar-category__icon-wrapper">
                                                <i class="fas fa-tag sidebar-category__icon"></i>
                                            </span>
                                            <span class="sidebar-category__content">
                                                <span class="sidebar-category__name">{{ $brand->name }}</span>
                                                <span class="category-count">{{ $brand->active_products_count ?? 0 }} items</span>
                                            </span>
                                            <i class="fas fa-chevron-right sidebar-category__arrow"></i>
                                        </label>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                        <!-- Tags Widget -->
                        @if(isset($tags) && $tags->count() > 0)
                        <div class="sidebar-widget">
                            <div class="sidebar-widget__header">
                                <h3 class="sidebar-widget__title">
                                    <i class="fas fa-hashtag sidebar-widget__icon"></i>
                                    Tags
                                </h3>
                            </div>
                            <div class="sidebar-widget__body">
                                <!-- Tag Search Box (only show if more than 10 tags) -->
                                @if($tags->count() > 10)
                                <div class="category-search-box mb-3">
                                    <input type="text" class="category-search-input" id="tagSearch" placeholder="Search tags...">
                                    <i class="fas fa-search category-search-icon"></i>
                                </div>
                                @endif

                                <!-- Tags List Container -->
                                <div class="categories-list-container" id="tagsListContainer">
                                    <ul class="sidebar-categories" id="tagsList">
                                        @foreach($tags as $index => $tag)
                                        <li class="sidebar-category {{ $index >= 10 ? 'category-item-hidden' : '' }}"
                                            data-category-name="{{ strtolower($tag->name) }}"
                                            data-category-index="{{ $index }}">
                                            <label class="sidebar-category__link sidebar-category__link--checkbox">
                                                <input class="tag-filter" type="checkbox" value="{{ $tag->id }}" id="tag{{ $tag->id }}"
                                                    {{ in_array($tag->id, $tagsFilter ?? []) ? 'checked' : '' }}>
                                                <span class="sidebar-category__icon-wrapper">
                                                    <i class="fas fa-hashtag sidebar-category__icon"></i>
                                                </span>
                                                <span class="sidebar-category__content">
                                                    <span class="sidebar-category__name">{{ $tag->name }}</span>
                                                    <span class="category-count">{{ $tag->products_count ?? 0 }} items</span>
                                                </span>
                                                <i class="fas fa-chevron-right sidebar-category__arrow"></i>
                                            </label>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Load More Button (only show if more than 10 tags) -->
                                @if($tags->count() > 10)
                                <div class="text-center mt-3">
                                    <button type="button" class="btn-load-more-categories" id="loadMoreTags" data-items-per-page="10">
                                        <span class="load-more-text">Load More</span>
                                        <span class="load-all-text" style="display: none;">Show All</span>
                                        <i class="fas fa-chevron-down ms-1"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Clear Filters Button -->
                        <div class="sidebar-widget">
                            <div class="sidebar-widget__body">
                                <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                                <button class="btn btn-link w-100 mt-2" id="clearFilters" style="text-decoration: none;">Clear All Filters</button>
                            </div>
                        </div>
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


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('sortSelect');
            const applyFiltersBtn = document.getElementById('applyFilters');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const priceRange = document.getElementById('priceRange');
            const priceMinDisplay = document.getElementById('priceMinDisplay');
            const priceMaxDisplay = document.getElementById('priceMaxDisplay');


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
                    document.querySelectorAll('.category-filter, .brand-filter, .tag-filter').forEach(cb => cb.checked = false);

                    // Reset price slider
                    if (priceRange) {
                        priceRange.value = '{{ $priceMax }}';
                        if (priceMaxDisplay) {
                            priceMaxDisplay.textContent = '{{ $priceMax }}';
                        }
                    }

                    // Redirect to clean URL
                    window.location.href = '{{ route("shop") }}';
                });
            }

            // Price filter functionality - update display as slider moves
            if (priceRange && priceMaxDisplay) {
                // Update max price display as slider moves
                priceRange.addEventListener('input', function() {
                    const maxValue = this.value;
                    priceMaxDisplay.textContent = maxValue;
                });
            }

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

                // Get selected tags
                const selectedTags = Array.from(document.querySelectorAll('.tag-filter:checked')).map(cb => cb.value);
                if (selectedTags.length > 0) {
                    currentUrl.searchParams.delete('tags');
                    selectedTags.forEach(id => {
                        currentUrl.searchParams.append('tags[]', id);
                    });
                } else {
                    currentUrl.searchParams.delete('tags');
                }

                // Get price range from slider if applied
                if (priceRange) {
                    const maxPrice = priceRange.value;
                    const priceMax = parseInt(priceRange.getAttribute('max')) || 100;
                    if (maxPrice < priceMax) {
                        currentUrl.searchParams.set('min_price', priceRange.getAttribute('min') || 0);
                        currentUrl.searchParams.set('max_price', maxPrice);
                    } else {
                        currentUrl.searchParams.delete('min_price');
                        currentUrl.searchParams.delete('max_price');
                    }
                }

                // Get sort
                if (sortSelect && sortSelect.value) {
                    currentUrl.searchParams.set('sort', sortSelect.value);
                }

                // Reset to page 1
                currentUrl.searchParams.delete('page');

                // Redirect
                window.location.href = currentUrl.toString();
            }
        });
    </script>
@endpush
@endsection

