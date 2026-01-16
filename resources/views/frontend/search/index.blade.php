@extends('layouts.frontend.main')

@section('content')
@include('frontend.partials.page-header', [
    'title' => $query ? 'Search Results for "' . $query . '"' : 'Search Results',
    'subtitle' => $query ? 'Found ' . $products->total() . ' result(s) for "' . $query . '"' : null,
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Search', 'url' => null]
    ]
])

<section class="category-products-section">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 col-md-4">
                <div class="category-sidebar">
                    <!-- Search Box Widget -->
                    <div class="sidebar-widget">
                        <div class="sidebar-widget__header">
                            <h3 class="sidebar-widget__title">Search</h3>
                        </div>
                        <div class="sidebar-widget__body">
                            <form method="GET" action="{{ route('search') }}" id="search-form">
                                <input type="text" 
                                       name="q" 
                                       class="form-control" 
                                       value="{{ $query }}" 
                                       placeholder="Search products...">
                                @if($category)
                                    <input type="hidden" name="category" value="{{ $category }}">
                                @endif
                                @if($sort)
                                    <input type="hidden" name="sort" value="{{ $sort }}">
                                @endif
                                <button type="submit" class="btn btn-primary btn-sm mt-2 w-100">Search</button>
                            </form>
                        </div>
                    </div>

                    <div class="sidebar-widget">
                        <div class="sidebar-widget__header">
                            <h3 class="sidebar-widget__title">Categories</h3>
                        </div>
                        <div class="sidebar-widget__body">
                            @if($categories && $categories->count() > 5)
                            <div class="category-search-box mb-3">
                                <input type="text" class="category-search-input" id="categorySearch" placeholder="Search categories...">
                                <i class="fas fa-search category-search-icon"></i>
                            </div>
                            @endif

                            <div class="categories-list-container" id="categoriesListContainer">
                                <ul class="sidebar-categories" id="categoriesList">
                                    @if($categories && $categories->count() > 0)
                                    <li class="sidebar-category">
                                        <a href="{{ route('search', array_merge(request()->except(['category', 'page']))) }}" 
                                           class="sidebar-category__link {{ !$category ? 'active' : '' }}">
                                            <span class="sidebar-category__name">All Categories</span>
                                        </a>
                                    </li>
                                    @foreach($categories as $catItem)
                                    <li class="sidebar-category" data-category-name="{{ strtolower($catItem->name) }}">
                                        <a href="{{ route('search', array_merge(request()->except(['category', 'page']), ['category' => $catItem->slug])) }}"
                                           class="sidebar-category__link {{ $category === $catItem->slug ? 'active' : '' }}">
                                            <span class="sidebar-category__name">{{ $catItem->name }}</span>
                                            <span class="category-count">({{ $catItem->active_products_count ?? 0 }})</span>
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
                    </div>

                    @include('frontend.partials.sidebar-price-filter', [
                        'priceMin' => $priceMin ?? 0,
                        'priceMax' => $priceMax ?? 100,
                        'minPrice' => $minPrice ?? null,
                        'maxPrice' => $maxPrice ?? null,
                        'showApplyButton' => true
                    ])
                </div>
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9 col-md-8">
                @include('frontend.partials.products-header', [
                    'products' => $products,
                    'sort' => $displaySort ?? 'featured',
                    'sortOptions' => [
                        'featured' => 'Sort by: Featured',
                        'price_low_high' => 'Price: Low to High',
                        'price_high_low' => 'Price: High to Low',
                        'name_asc' => 'Name: A to Z',
                        'name_desc' => 'Name: Z to A',
                        'newest' => 'Newest First',
                    ]
                ])

                @include('frontend.partials.products-grid', [
                    'products' => $products,
                    'emptyMessage' => 'No products found. Try adjusting your search or filters.'
                ])
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sortSelect');
        const priceRange = document.getElementById('priceRange');
        const priceMinDisplay = document.getElementById('priceMinDisplay');
        const priceMaxDisplay = document.getElementById('priceMaxDisplay');
        const applyPriceFilter = document.getElementById('applyPriceFilter');
        const clearPriceFilter = document.getElementById('clearPriceFilter');

        // Category search functionality
        const categorySearch = document.getElementById('categorySearch');
        if (categorySearch) {
            categorySearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const categoryItems = document.querySelectorAll('.sidebar-category');
                
                categoryItems.forEach(item => {
                    const categoryName = item.getAttribute('data-category-name') || '';
                    if (categoryName.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Sort functionality
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const selectedSort = this.value;
                const currentUrl = new URL(window.location.href);
                
                // Map sort values back to search format
                const sortMap = {
                    'featured': 'relevance',
                    'price_low_high': 'price_asc',
                    'price_high_low': 'price_desc',
                    'name_asc': 'name_asc',
                    'name_desc': 'name_desc',
                    'newest': 'newest'
                };
                
                currentUrl.searchParams.set('sort', sortMap[selectedSort] || 'relevance');
                currentUrl.searchParams.delete('page');
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

                    if (maxPrice < priceMax) {
                        currentUrl.searchParams.set('min_price', priceMin);
                        currentUrl.searchParams.set('max_price', maxPrice);
                    } else {
                        currentUrl.searchParams.delete('min_price');
                        currentUrl.searchParams.delete('max_price');
                    }

                    currentUrl.searchParams.delete('page');
                    window.location.href = currentUrl.toString();
                });
            }

            // Clear price filter
            if (clearPriceFilter) {
                clearPriceFilter.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('min_price');
                    currentUrl.searchParams.delete('max_price');
                    currentUrl.searchParams.delete('page');
                    window.location.href = currentUrl.toString();
                });
            }
        }

        // Check wishlist and cart status for products on page load
        if (window.WishlistFunctions && typeof window.WishlistFunctions.checkWishlistStatus === 'function') {
            window.WishlistFunctions.checkWishlistStatus();
        }
        
        if (window.CartFunctions && typeof window.CartFunctions.checkCartStatus === 'function') {
            window.CartFunctions.checkCartStatus();
        }
    });
</script>
@endpush

