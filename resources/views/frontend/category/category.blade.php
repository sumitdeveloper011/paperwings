@extends('layouts.frontend.main')
@section('content')
    @include('include.frontend.breadcrumb')
    <section class="category-products-section">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 col-md-4">
                    <div class="category-sidebar">
                        <div class="sidebar-widget">
                            <div class="sidebar-widget__header">
                                <h3 class="sidebar-widget__title">
                                    <i class="fas fa-th-large sidebar-widget__icon"></i>
                                    Categories
                                </h3>
                            </div>
                            <div class="sidebar-widget__body">
                                <ul class="sidebar-categories">
                                    @if($categories && $categories->count() > 0)
                                    @foreach($categories as $category)
                                    <li class="sidebar-category">
                                        <a href="{{ route('product.by.category', $category->slug) }}"
                                           class="sidebar-category__link {{ request()->route('product.by.category') && request()->route('slug') == $category->slug ? 'active' : '' }}">
                                            <span class="sidebar-category__icon-wrapper">
                                                <i class="fas fa-folder sidebar-category__icon"></i>
                                            </span>
                                            <span class="sidebar-category__content">
                                                <span class="sidebar-category__name">{{ $category->name }}</span>
                                                <span class="category-count">{{ $category->products_count ?? 0 }} items</span>
                                            </span>
                                            <i class="fas fa-chevron-right sidebar-category__arrow"></i>
                                        </a>
                                    </li>
                                    @endforeach
                                    @else
                                    <li class="sidebar-category--empty">
                                        <span class="sidebar-category__empty-text">No categories available</span>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

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
                                            <span class="price-range-display__value">$<span id="priceMinDisplay">{{ $priceMin ?? 0 }}</span></span>
                                        </div>
                                        <div class="price-range-display__divider">-</div>
                                        <div class="price-range-display__item">
                                            <span class="price-range-display__label">Max</span>
                                            <span class="price-range-display__value">$<span id="priceMaxDisplay">{{ $maxPrice ?? ($priceMax ?? 100) }}</span></span>
                                        </div>
                                    </div>
                                    <div class="price-range-slider">
                                        <input type="range" class="price-range" id="priceRange"
                                               min="{{ $priceMin ?? 0 }}"
                                               max="{{ $priceMax ?? 100 }}"
                                               value="{{ $maxPrice ?? ($priceMax ?? 100) }}"
                                               step="1">
                                    </div>
                                    <div class="price-filter-actions">
                                        <button type="button" class="price-filter-btn price-filter-btn--primary" id="applyPriceFilter">
                                            <i class="fas fa-check"></i>
                                            Apply Filter
                                        </button>
                                        @if($minPrice || $maxPrice)
                                        <button type="button" class="price-filter-btn price-filter-btn--secondary" id="clearPriceFilter">
                                            <i class="fas fa-times"></i>
                                            Clear
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8">
                    <div class="products-header">
                        <div class="products-header__left">
                            <p class="products-count">Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products</p>
                        </div>
                        <div class="products-header__right">
                            <div class="sort-dropdown">
                                <select class="sort-select" id="sortSelect">
                                    <option value="featured" {{ (isset($sort) && $sort == 'featured') ? 'selected' : '' }}>Sort by: Featured</option>
                                    <option value="price_low_high" {{ (isset($sort) && $sort == 'price_low_high') ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high_low" {{ (isset($sort) && $sort == 'price_high_low') ? 'selected' : '' }}>Price: High to Low</option>
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
                                    <span class="cute-stationery__price-current">${{ $product->discount_price }}</span>
                                    <span class="cute-stationery__price-old">${{ $product->total_price }}</span>
                                    @else
                                    <span class="cute-stationery__price-current">${{ $product->total_price }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="products-grid__empty">
                            <p>No products found in this category.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('sortSelect');
            const priceRange = document.getElementById('priceRange');
            const priceMinDisplay = document.getElementById('priceMinDisplay');
            const priceMaxDisplay = document.getElementById('priceMaxDisplay');
            const applyPriceFilter = document.getElementById('applyPriceFilter');

            // Sort functionality
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    const selectedSort = this.value;
                    const currentUrl = new URL(window.location.href);

                    // Update or add sort parameter
                    currentUrl.searchParams.set('sort', selectedSort);

                    // Reset to page 1 when sorting changes
                    currentUrl.searchParams.delete('page');

                    // Redirect to new URL with sort parameter
                    window.location.href = currentUrl.toString();
                });
            }

            // Price filter functionality
            if (priceRange && priceMaxDisplay) {
                const priceMin = parseInt(priceRange.getAttribute('min')) || 0;
                const priceMax = parseInt(priceRange.getAttribute('max')) || 100;

                // Update max price display as slider moves
                priceRange.addEventListener('input', function() {
                    const maxValue = this.value;
                    priceMaxDisplay.textContent = maxValue;
                });

                // Apply price filter
                if (applyPriceFilter) {
                    applyPriceFilter.addEventListener('click', function() {
                        const currentUrl = new URL(window.location.href);
                        const maxPrice = priceRange.value;

                        // Only apply filter if max price is less than the maximum available price
                        if (maxPrice < priceMax) {
                            // Set price parameters
                            currentUrl.searchParams.set('min_price', priceMin);
                            currentUrl.searchParams.set('max_price', maxPrice);
                        } else {
                            // If max price equals the maximum, remove filter
                            currentUrl.searchParams.delete('min_price');
                            currentUrl.searchParams.delete('max_price');
                        }

                        // Reset to page 1 when filter changes
                        currentUrl.searchParams.delete('page');

                        // Redirect to new URL with price filter
                        window.location.href = currentUrl.toString();
                    });
                }

                // Clear price filter
                const clearPriceFilter = document.getElementById('clearPriceFilter');
                if (clearPriceFilter) {
                    clearPriceFilter.addEventListener('click', function() {
                        const currentUrl = new URL(window.location.href);

                        // Remove price parameters
                        currentUrl.searchParams.delete('min_price');
                        currentUrl.searchParams.delete('max_price');

                        // Reset to page 1 when filter changes
                        currentUrl.searchParams.delete('page');

                        // Reset slider to max value
                        priceRange.value = priceMax;
                        priceMaxDisplay.textContent = priceMax;

                        // Redirect to new URL without price filter
                        window.location.href = currentUrl.toString();
                    });
                }
            }
        });
    </script>
@endsection
