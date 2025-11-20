@extends('layouts.frontend.main')
@section('content')
    <section class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $product->name ?? 'Page Title' }}</li>
                        </ol>
                    </nav>
                    <h1 class="page-title">{{ $product->name ?? 'Page Title' }}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="product-details-section">
        <div class="container">
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-images">
                        <div class="product-main-image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Premium Notebook" class="main-img" id="mainImage">
                        </div>
                        <div class="product-thumbnails">
                            <div class="thumbnail-item active" data-image="assets/images/product-1.jpg">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Premium Notebook">
                            </div>
                            <div class="thumbnail-item" data-image="assets/images/product-2.jpg">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Premium Notebook">
                            </div>
                            <div class="thumbnail-item" data-image="assets/images/product-3.jpg">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Premium Notebook">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <h1 class="product-title">{{ $product->name }}</h1>
                        <div class="product-rating">
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="rating-text">4.5 out of 5</span>
                            <span class="reviews-count">(24 reviews)</span>
                        </div>

                        <div class="product-price">

                            @if($product->discount_price)
                                <span class="current-price">${{ $product->discount_price }}</span>
                                <span class="discount">Save {{ round(($product->total_price - $product->discount_price) / $product->total_price * 100) }}%</span>
                            @else
                                <span class="current-price">${{ $product->total_price }}</span>
                            @endif
                        </div>

                        <div class="product-description">
                            <p>{!! $product->short_description !!}</p>
                        </div>

                        <div class="product-options">
                            <div class="option-group">
                                <label class="option-label">Quantity:</label>
                                <div class="quantity-selector">
                                    <button class="qty-btn" id="decreaseQty">-</button>
                                    <input type="number" value="1" min="1" max="99" id="quantity">
                                    <button class="qty-btn" id="increaseQty">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="product-actions">
                            <button class="btn btn-primary add-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                            <button class="btn btn-outline-primary add-to-wishlist">
                                <i class="fas fa-heart"></i>
                                Add to Wishlist
                            </button>
                        </div>

                        <div class="product-meta">
                            <div class="meta-item">
                                <span class="meta-label">SKU:</span>
                                <span class="meta-value">{{ $product->barcode }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Category:</span>
                                <span class="meta-value">{{ $product->category->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="product-tabs">
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            @if($product->accordions->count() > 0)
                                @foreach($product->accordions as $accordion)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="accordion-{{ $accordion->id }}-tab" data-bs-toggle="tab" data-bs-target="#accordion-{{ $accordion->id }}" type="button" role="tab">{{ $accordion->heading }}</button>
                                    </li>
                                @endforeach
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews</button>
                                </li>
                            @endif
                        </ul>

                        <div class="tab-content" id="productTabsContent">
                            @if($product->accordions->count() > 0)
                                @foreach($product->accordions as $accordion)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="accordion-{{ $accordion->id }}" role="tabpanel">
                                        <div class="tab-content-body">
                                            <h3>{{ $accordion->heading }}</h3>
                                            {!! $accordion->content !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="tab-content-body">
                                    <h3>Customer Reviews</h3>
                                    <div class="reviews-summary">
                                        <div class="overall-rating">
                                            <div class="rating-number">4.5</div>
                                            <div class="rating-stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                            <div class="total-reviews">Based on 24 reviews</div>
                                        </div>
                                    </div>

                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-name">Sarah M.</div>
                                            <div class="review-rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="review-date">March 15, 2024</div>
                                        </div>
                                        <div class="review-content">
                                            <p>Excellent quality notebook! The paper is smooth and the cover is very durable. Perfect for my daily journaling needs.</p>
                                        </div>
                                    </div>

                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="reviewer-name">John D.</div>
                                            <div class="review-rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                            <div class="review-date">March 10, 2024</div>
                                        </div>
                                        <div class="review-content">
                                            <p>Great notebook for taking notes in meetings. The lay-flat binding makes it easy to write on every page.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="related-products">
                        <h3 class="section-title">You May Also Like</h3>
                        <div class="row">
                            @if($relatedProducts->count() > 0)
                                @foreach($relatedProducts as $relatedProduct)
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div class="product-item">
                                            <div class="product__image">
                                                <a href="{{ route('product.detail', $relatedProduct->slug) }}">
                                                    <img src="{{ $relatedProduct->main_image }}" alt="{{ $relatedProduct->name }}" class="product__img">
                                                </a>
                                                <div class="product__actions">
                                                    <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                                    <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                                </div>
                                            </div>
                                            <div class="product__info">
                                                <h3 class="product__name">
                                                    <a href="{{ route('product.detail', $relatedProduct->slug) }}">
                                                        {{ $relatedProduct->name }}
                                                    </a>
                                                </h3>
                                                <div class="product__price">
                                                    <span class="product__price-current">${{ $relatedProduct->total_price }}</span>
                                                    @if($relatedProduct->discount_price)
                                                        <span class="product__price-old">${{ $relatedProduct->total_price }}</span>
                                                    @else
                                                        <span class="product__price-current">${{ $relatedProduct->total_price }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
