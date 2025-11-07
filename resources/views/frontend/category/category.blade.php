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
                                <li class="sidebar-category">
                                    <a href="#" class="sidebar-category__link active">
                                        <i class="fas fa-pen"></i>
                                        Pens & Pencils
                                        <span class="category-count">(24)</span>
                                        <i class="fas fa-chevron-down category-toggle"></i>
                                    </a>
                                    <ul class="sidebar-subcategories">
                                        <li><a href="#" class="sidebar-subcategory__link">Fountain Pens (8)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Ballpoint Pens (6)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Gel Pens (4)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Mechanical Pencils (3)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Wooden Pencils (3)</a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-category">
                                    <a href="#" class="sidebar-category__link">
                                        <i class="fas fa-book"></i>
                                        Notebooks
                                        <span class="category-count">(18)</span>
                                        <i class="fas fa-chevron-down category-toggle"></i>
                                    </a>
                                    <ul class="sidebar-subcategories">
                                        <li><a href="#" class="sidebar-subcategory__link">Spiral Notebooks (8)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Composition Books (5)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Loose Leaf Paper (3)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Sticky Notes (2)</a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-category">
                                    <a href="#" class="sidebar-category__link">
                                        <i class="fas fa-calculator"></i>
                                        Calculators
                                        <span class="category-count">(12)</span>
                                        <i class="fas fa-chevron-down category-toggle"></i>
                                    </a>
                                    <ul class="sidebar-subcategories">
                                        <li><a href="#" class="sidebar-subcategory__link">Scientific (6)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Basic (4)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Graphing (2)</a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-category">
                                    <a href="#" class="sidebar-category__link">
                                        <i class="fas fa-calendar"></i>
                                        Calendars
                                        <span class="category-count">(8)</span>
                                        <i class="fas fa-chevron-down category-toggle"></i>
                                    </a>
                                    <ul class="sidebar-subcategories">
                                        <li><a href="#" class="sidebar-subcategory__link">Wall Calendars (4)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Desk Calendars (2)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Planners (2)</a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-category">
                                    <a href="#" class="sidebar-category__link">
                                        <i class="fas fa-ruler"></i>
                                        Rulers & Scales
                                        <span class="category-count">(15)</span>
                                        <i class="fas fa-chevron-down category-toggle"></i>
                                    </a>
                                    <ul class="sidebar-subcategories">
                                        <li><a href="#" class="sidebar-subcategory__link">Metal Rulers (6)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Plastic Rulers (5)</a></li>
                                        <li><a href="#" class="sidebar-subcategory__link">Scale Rulers (4)</a></li>
                                    </ul>
                                </li>
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

                        <div class="sidebar-widget">
                            <h3 class="sidebar-widget__title">Brands</h3>
                            <div class="brand-filter">
                                <label class="brand-checkbox">
                                    <input type="checkbox" checked>
                                    <span class="checkmark"></span>
                                    Faber-Castell
                                </label>
                                <label class="brand-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                    Pilot
                                </label>
                                <label class="brand-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                    Pentel
                                </label>
                                <label class="brand-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                    Staedtler
                                </label>
                                <label class="brand-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                    Muji
                                </label>
                            </div>
                        </div>

                        <div class="sidebar-widget">
                            <h3 class="sidebar-widget__title">Clear Filters</h3>
                            <button class="clear-filters-btn">Clear All</button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="col-lg-9 col-md-8">
                    <div class="products-header">
                        <div class="products-header__left">
                            <p class="products-count">Showing 1-12 of 48 products</p>
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
                            <div class="view-toggle">
                                <button class="view-btn view-btn--grid active" data-view="grid">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button class="view-btn view-btn--list" data-view="list">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="products-grid" id="productsGrid">
                        <!-- Product Item 1 -->
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Product" class="product__img">
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
                                <h3 class="product__name">Premium Fountain Pen</h3>
                                <div class="product__price">
                                    <span class="product__price-current">$29.99</span>
                                    <span class="product__price-old">$39.99</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Item 2 -->
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Product" class="product__img">
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
                                <h3 class="product__name">Leather Notebook</h3>
                                <div class="product__price">
                                    <span class="product__price-current">$24.99</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Item 3 -->
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Product" class="product__img">
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
                                <h3 class="product__name">Scientific Calculator</h3>
                                <div class="product__price">
                                    <span class="product__price-current">$19.99</span>
                                    <span class="product__price-old">$24.99</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Item 4 -->
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Product" class="product__img">
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
                                <h3 class="product__name">Gel Pen Set</h3>
                                <div class="product__price">
                                    <span class="product__price-current">$14.99</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Item 5 -->
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Product" class="product__img">
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
                                <h3 class="product__name">Planner Organizer</h3>
                                <div class="product__price">
                                    <span class="product__price-current">$34.99</span>
                                    <span class="product__price-old">$44.99</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Item 6 -->
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Product" class="product__img">
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
                                <h3 class="product__name">Pencil Case</h3>
                                <div class="product__price">
                                    <span class="product__price-current">$9.99</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Item 7 -->
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Product" class="product__img">
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
                                <h3 class="product__name">Desk Calendar</h3>
                                <div class="product__price">
                                    <span class="product__price-current">$12.99</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Item 8 -->
                        <div class="product-item">
                            <div class="product__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Product" class="product__img">
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
                                <h3 class="product__name">Sticky Notes</h3>
                                <div class="product__price">
                                    <span class="product__price-current">$7.99</span>
                                    <span class="product__price-old">$9.99</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-wrapper">
                        <nav aria-label="Products pagination">
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">4</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection