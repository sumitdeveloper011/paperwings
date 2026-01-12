@extends('layouts.frontend.main')

@section('content')
@include('frontend.partials.page-header', [
    'title' => 'Search Results',
    'subtitle' => $query ? 'Found ' . $products->total() . ' result(s) for "' . $query . '"' : null,
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Search', 'url' => null]
    ]
])

<section class="search-results-section">
    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="search-filters">
                    <h3 class="search-filters__title">Filters</h3>
                    
                    <!-- Search Box -->
                    <div class="search-filter-group">
                        <label class="search-filter-label">Search</label>
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
                        </form>
                    </div>

                    <!-- Category Filter -->
                    @if($categories->count() > 0)
                    <div class="search-filter-group">
                        <label class="search-filter-label">Category</label>
                        <div class="search-filter-options">
                            <a href="{{ route('search', array_merge(request()->query(), ['category' => null])) }}" 
                               class="search-filter-option {{ !$category ? 'active' : '' }}">
                                All Categories
                            </a>
                            @foreach($categories as $cat)
                            <a href="{{ route('search', array_merge(request()->query(), ['category' => $cat->slug])) }}" 
                               class="search-filter-option {{ $category === $cat->slug ? 'active' : '' }}">
                                {{ $cat->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Price Range -->
                    <div class="search-filter-group">
                        <label class="search-filter-label">Price Range</label>
                        <form method="GET" action="{{ route('search') }}" class="price-filter-form">
                            @if($query)
                                <input type="hidden" name="q" value="{{ $query }}">
                            @endif
                            @if($category)
                                <input type="hidden" name="category" value="{{ $category }}">
                            @endif
                            <div class="price-inputs">
                                <input type="number" 
                                       name="min_price" 
                                       class="form-control" 
                                       placeholder="Min" 
                                       value="{{ $minPrice }}">
                                <span>-</span>
                                <input type="number" 
                                       name="max_price" 
                                       class="form-control" 
                                       placeholder="Max" 
                                       value="{{ $maxPrice }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Apply</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div class="col-lg-9">
                <!-- Sort Options -->
                <div class="search-results-header">
                    <div class="search-results-count">
                        <strong>{{ $products->total() }}</strong> product(s) found
                    </div>
                    <div class="search-results-sort">
                        <form method="GET" action="{{ route('search') }}" id="sort-form">
                            @if($query)
                                <input type="hidden" name="q" value="{{ $query }}">
                            @endif
                            @if($category)
                                <input type="hidden" name="category" value="{{ $category }}">
                            @endif
                            <select name="sort" class="form-control" onchange="this.form.submit()">
                                <option value="relevance" {{ $sort === 'relevance' ? 'selected' : '' }}>Relevance</option>
                                <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                                <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name: Z to A</option>
                                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest First</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                    <div class="products-grid">
                        @foreach($products as $product)
                        <div class="product-card">
                            <div class="product-card__image">
                                <a href="{{ route('product.detail', $product->slug) }}">
                                    <img src="{{ $product->main_thumbnail_url }}" alt="{{ $product->name }}">
                                </a>
                                @if($product->discount_price)
                                    <span class="product-card__badge">Sale</span>
                                @endif
                                <div class="product-card__actions">
                                    <button class="product-card__action wishlist-btn" data-product-id="{{ $product->id }}" title="Add to Wishlist">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="product-card__action add-to-cart" data-product-id="{{ $product->id }}" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product-card__content">
                                <h3 class="product-card__title">
                                    <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                @if($product->category)
                                    <p class="product-card__category">{{ $product->category->name }}</p>
                                @endif
                                <div class="product-card__price">
                                    @if($product->discount_price)
                                        <span class="product-card__price--old">${{ number_format($product->total_price, 2) }}</span>
                                        <span class="product-card__price--new">${{ number_format($product->discount_price, 2) }}</span>
                                    @else
                                        <span class="product-card__price--current">${{ number_format($product->total_price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        @include('include.frontend.pagination', ['paginator' => $products])
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-search fa-3x"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your search or filters.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Back to Home</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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

