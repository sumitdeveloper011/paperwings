@extends('layouts.frontend.main')
@section('content')
<!-- Slider Section -->
    <section class="slider-section">
        <div class="slider">
            @if($sliders && $sliders->count() > 0)
                @foreach($sliders as $slider)
                    <div class="slider__slide" style="background-image: url('{{ asset('storage/' . $slider->image) }}');">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="slider__content">
                                        <div class="slider__tagline">{{ $slider->sub_heading }}</div>
                                        <h1 class="slider__heading">{{ $slider->heading }}</h1>
                                        <a href="{{ $slider->buttons[0]['url'] }}" class="slider__btn">
                                            {{ $slider->buttons[0]['name'] }} <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @else
                <div class="slider__slide" style="background-image: url('{{ asset('assets/frontend/images/banner-1.jpg') }}');">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="slider__content">
                                    <div class="slider__tagline">Welcome to Paper Wings</div>
                                    <h1 class="slider__heading">Welcome to Paper Wings</h1>
                                    <a href="#" class="slider__btn">Shop Now →</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Shop By Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="categories__header text-center">
                        <h2 class="categories__title">Shop By Categories</h2>
                        <p class="categories__description">Essential Office Supplies In Our Online Stationery Shop That Keep Your Office Operations Smooth And Efficient</p>
                    </div>
                </div>
            </div>
            <div class="row">
                @if($categories && $categories->count() > 0)
                @foreach($categories as $category)
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="category-item">
                            <div class="category__image">
                                <a href="{{ route('product.by.category', $category->slug) }}">
                                    <img src="{{ asset('storage/' . $category->image) ?? asset('assets/frontend/images/office-supplies.jpg') }}" alt="{{ $category->name }}" class="category__img">
                                </a>
                            </div>
                            <h3 class="category__name">
                                <a href="{{ route('product.by.category', $category->slug) }}">{{ $category->name }}</a>
                            </h3>
                        </div>
                    </div>
                @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- Promotional Banners Section -->
    <section class="promo-banners-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="promo-banner promo-banner--home-office">
                        <div class="promo-banner__content">
                            <div class="promo-banner__badge">Sale Up To 15% Off</div>
                            <h3 class="promo-banner__title">
                                <span class="promo-banner__title-line">Home Office</span>
                                <span class="promo-banner__title-line">Desks</span>
                            </h3>
                            <a href="#" class="promo-banner__btn">Shop Now →</a>
                        </div>
                        <div class="promo-banner__illustration">
                            <!-- Home office illustration elements -->
                            <div class="promo-banner__desk"></div>
                            <div class="promo-banner__chair"></div>
                            <div class="promo-banner__lamp"></div>
                            <div class="promo-banner__shelf"></div>
                            <div class="promo-banner__plant"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="promo-banner promo-banner--tape">
                        <div class="promo-banner__content">
                            <div class="promo-banner__badge">Office Adhesive</div>
                            <h3 class="promo-banner__title">Tape</h3>
                            <div class="promo-banner__price">From $12.99</div>
                            <a href="#" class="promo-banner__btn">Shop Now →</a>
                        </div>
                        <div class="promo-banner__illustration">
                            <!-- Tape illustration elements -->
                            <div class="promo-banner__tape-roll promo-banner__tape-roll--1"></div>
                            <div class="promo-banner__tape-roll promo-banner__tape-roll--2"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="promo-banner promo-banner--notebooks">
                        <div class="promo-banner__content">
                            <div class="promo-banner__badge">All Page Types</div>
                            <h3 class="promo-banner__title">Notebooks</h3>
                            <div class="promo-banner__price">25% Off</div>
                            <a href="#" class="promo-banner__btn">Shop Now →</a>
                        </div>
                        <div class="promo-banner__illustration">
                            <!-- Notebooks illustration elements -->
                            <div class="promo-banner__notebook promo-banner__notebook--1"></div>
                            <div class="promo-banner__notebook promo-banner__notebook--2"></div>
                            <div class="promo-banner__pen"></div>
                            <div class="promo-banner__sprig"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="promo-banner promo-banner--metal-pens">
                        <div class="promo-banner__content">
                            <div class="promo-banner__badge">Office / Home</div>
                            <h3 class="promo-banner__title">Metal Pens</h3>
                            <div class="promo-banner__price">15% Off</div>
                            <a href="#" class="promo-banner__btn">Shop Now →</a>
                        </div>
                        <div class="promo-banner__illustration">
                            <!-- Metal pens illustration elements -->
                            <div class="promo-banner__pen promo-banner__pen--1"></div>
                            <div class="promo-banner__pen promo-banner__pen--2"></div>
                            <div class="promo-banner__pen promo-banner__pen--3"></div>
                            <div class="promo-banner__pen promo-banner__pen--4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section with Tabs -->
    <section class="products-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="products__header">
                        <h2 class="products__title">Our Products</h2>
                        <div class="products__tabs">
                            <button class="products__tab products__tab--active" data-tab="featured">Featured</button>
                            <button class="products__tab" data-tab="on-sale">On Sale</button>
                            <button class="products__tab" data-tab="top-rated">Top Rated</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Featured Products Tab -->
            <div class="products__content products__content--active" id="featured">
                @if($featuredProducts && $featuredProducts->count() > 0)
                <div class="owl-carousel products-carousel">
                    @foreach($featuredProducts as $featuredProduct)
                    <div class="cute-stationery__item">
                        <div class="cute-stationery__image">
                            <a href="{{ route('product.detail', $featuredProduct->slug) }}" class="cute-stationery__image-link">
                            <img src="{{ $featuredProduct->main_image }}" alt="{{ $featuredProduct->name }}" class="cute-stationery__img">
                            </a>
                            <div class="cute-stationery__actions">
                                <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $featuredProduct->id }}" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="cute-stationery__action cute-stationery__add-cart add-to-cart" data-product-id="{{ $featuredProduct->id }}" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="cute-stationery__info">
                            <h3 class="cute-stationery__name">
                                <a href="{{ route('product.detail', $featuredProduct->slug) }}" class="cute-stationery__name-link">
                                {{ $featuredProduct->name }}
                                </a>
                            </h3>
                            <div class="cute-stationery__price">
                                @if($featuredProduct->discount_price)
                                <span class="cute-stationery__price-current">${{ $featuredProduct->discount_price }}</span>
                                <span class="cute-stationery__price-old">${{ $featuredProduct->total_price }}</span>
                                @else
                                <span class="cute-stationery__price-current">${{ $featuredProduct->total_price }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-center">No Featured Products Found</p>
                @endif
            </div>

            <!-- On Sale Products Tab -->
            <div class="products__content" id="on-sale">
                @if($onSaleProducts && $onSaleProducts->count() > 0)
                <div class="owl-carousel products-carousel">
                    @foreach($onSaleProducts as $onSaleProduct)
                    <div class="cute-stationery__item">
                        <div class="cute-stationery__image">
                            <a href="{{ route('product.detail', $onSaleProduct->slug) }}" class="cute-stationery__image-link">
                            <img src="{{ $onSaleProduct->main_image }}" alt="{{ $onSaleProduct->name }}" class="cute-stationery__img">
                            </a>
                            <div class="cute-stationery__actions">
                                <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $onSaleProduct->id }}" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="cute-stationery__action cute-stationery__add-cart add-to-cart" data-product-id="{{ $onSaleProduct->id }}" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="cute-stationery__info">
                            <h3 class="cute-stationery__name">
                                <a href="{{ route('product.detail', $onSaleProduct->slug) }}" class="cute-stationery__name-link">
                                {{ $onSaleProduct->name }}
                                </a>
                            </h3>
                            <div class="cute-stationery__price">
                                @if($onSaleProduct->discount_price)
                                <span class="cute-stationery__price-current">${{ $onSaleProduct->discount_price }}</span>
                                <span class="cute-stationery__price-old">${{ $onSaleProduct->total_price }}</span>
                                @else
                                <span class="cute-stationery__price-current">${{ $onSaleProduct->total_price }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-center">No On Sale Products Found</p>
                @endif
            </div>

            <!-- Top Rated Products Tab -->
            <div class="products__content" id="top-rated">
                @if($topRatedProducts && $topRatedProducts->count() > 0)
                <div class="owl-carousel products-carousel">
                    @foreach($topRatedProducts as $topRatedProduct)
                    <div class="cute-stationery__item">
                        <div class="cute-stationery__image">
                            <a href="{{ route('product.detail', $topRatedProduct->slug) }}" class="cute-stationery__image-link">
                            <img src="{{ $topRatedProduct->main_image }}" alt="{{ $topRatedProduct->name }}" class="cute-stationery__img">
                            </a>
                            <div class="cute-stationery__actions">
                                <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $topRatedProduct->id }}" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="cute-stationery__action cute-stationery__add-cart add-to-cart" data-product-id="{{ $topRatedProduct->id }}" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="cute-stationery__info">
                            <h3 class="cute-stationery__name">
                                <a href="{{ route('product.detail', $topRatedProduct->slug) }}" class="cute-stationery__name-link">
                                {{ $topRatedProduct->name }}
                                </a>
                            </h3>
                            <div class="cute-stationery__price">
                                @if($topRatedProduct->discount_price)
                                <span class="cute-stationery__price-current">${{ $topRatedProduct->discount_price }}</span>
                                <span class="cute-stationery__price-old">${{ $topRatedProduct->total_price }}</span>
                                @else
                                <span class="cute-stationery__price-current">${{ $topRatedProduct->total_price }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <p class="text-center">No Top Rated Products Found</p>
                 @endif
            </div>
        </div>
    </section>

    <!-- Experience Banner Section -->
    <section class="experience-banner">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="experience-banner__content">
                        <div class="experience-banner__text">
                            <div class="experience-banner__badge">100% STATIONERY PRODUCT</div>
                            <h2 class="experience-banner__title">
                                <span class="experience-banner__title-line">Open Up To A New</span>
                                <span class="experience-banner__title-line">Experience.</span>
                            </h2>
                            <a href="#" class="experience-banner__btn">All Collections <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about__image">
                        <img src="{{ asset('assets/frontend/images/about-us.jpg') }}" alt="Person Writing" class="about__img">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about__content">
                        <div class="about__badge">THE STATIONERO</div>
                        <h2 class="about__title">The Stationery Company</h2>
                        <p class="about__description">Our Office Supplies Will Help You Organize Your Workspace From All Kinds Of Desk Essentials To Top Quality Staplers, Calculators And Organizers.</p>
                        <a href="#" class="about__btn">Find Out More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cute Stationery Section -->
    <section class="cute-stationery-section">
        <div class="container-fluid">
            <div class="cute-stationery__header">
                <h2 class="cute-stationery__title">Cute Stationery</h2>
                <div class="cute-stationery__nav">
                    @if($randomCategories && $randomCategories->count() > 0)
                        @foreach($randomCategories as $category)
                        <button class="cute-stationery__nav-item {{ $loop->first ? 'active' : '' }}" data-category="{{ $category->slug }}">{{ $category->name }}</button>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="cute-stationery__content">
                @if($randomCategories && $randomCategories->count() > 0)
                @foreach($randomCategories as $category)
                <div class="cute-stationery__tab-content {{ $loop->first ? 'active' : '' }}" id="{{ $category->slug }}-content">
                    <div class="owl-carousel cute-stationery-carousel">
                        @foreach($categoryProducts[$category->id] as $product)
                            <div class="cute-stationery__item">
                                <div class="cute-stationery__image">
                                    <a href="{{ route('product.detail', $product->slug) }}" class="cute-stationery__image-link">
                                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="cute-stationery__img">
                                    </a>
                                    <div class="cute-stationery__actions">
                                        <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $product->id }}" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
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
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- Subscription Banner Section -->
    <section class="subscription-banner">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="subscription-banner__content">
                        <h2 class="subscription-banner__title">
                            <span class="subscription-banner__title-line">Stay Updated with Special Offers</span>
                        </h2>
                        <div class="subscription-banner__form">
                            <form class="subscription-form">
                                <div class="subscription-form__input-group">
                                    <input type="email" class="subscription-form__input" placeholder="Enter Your Email Address" required>
                                    <button type="submit" class="subscription-form__btn">Subscribe</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
