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
                            <h3 class="sidebar-widget__title">Categories</h3>
                            <ul class="sidebar-categories">
                                @if($categories && $categories->count() > 0)
                                @foreach($categories as $category)
                                <li class="sidebar-category">
                                    <a href="{{ route('product.by.category', $category->slug) }}" class="sidebar-category__link">
                                        <i class="fas fa-folder"></i>
                                        {{ $category->name }}
                                        <span class="category-count">({{ $category->products_count ?? 0 }})</span>
                                        <i class="fas fa-chevron-down category-toggle"></i>
                                    </a>
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>

                        <div class="sidebar-widget">
                            <h3 class="sidebar-widget__title">Price Range</h3>
                            <div class="price-filter">
                                <div class="price-range-display">
                                    <span class="price-min">$0</span>
                                    <span class="price-max">$100</span>
                                </div>
                                <div class="price-range-slider">
                                    <input type="range" class="price-range" id="priceRange" min="0" max="100" value="100" step="5">
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
                                <select class="sort-select">
                                    <option>Sort by: Featured</option>
                                    <option>Price: Low to High</option>
                                    <option>Price: High to Low</option>
                                    <option>Newest First</option>
                                    <option>Best Selling</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="products-grid" id="productsGrid">

                        @if($products && $products->count() > 0)
                        @foreach($products as $product)
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="product__img">
                                <div class="product__actions">
                                    <button class="product__action" title="Add to Wishlist">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="product__add-cart" title="Add to Cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="product__info">
                                <h3 class="product__name">{{ $product->name }}</h3>
                                <div class="product__price">
                                    @if($product->discount_price)
                                    <span class="product__price-current">${{ $product->discount_price }}</span>
                                    <span class="product__price-old">${{ $product->total_price }}</span>
                                    @else
                                    <span class="product__price-current">${{ $product->total_price }}</span>
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
@endsection
