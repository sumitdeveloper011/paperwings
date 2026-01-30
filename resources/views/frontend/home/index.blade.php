@extends('layouts.frontend.main')

@push('head')
@php
    $settings = \App\Helpers\SettingHelper::all();
    $siteLogo = \App\Helpers\SettingHelper::logo();
@endphp

<!-- Schema.org Organization -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Organization",
  "name": "{{ config('app.name') }}",
  "url": "{{ url('/') }}",
  "logo": "{{ $siteLogo }}",
  "address": {
    "@@type": "PostalAddress",
    "addressCountry": "NZ"
  }
}
</script>
@endpush

@section('content')
<!-- Slider Section -->
    <section class="slider-section">
        <div class="swiper main-banner-slider">
            <div class="swiper-wrapper">
                @if($sliders && $sliders->count() > 0)
                    @foreach($sliders as $slider)
                        <div class="swiper-slide">
                            <div class="slider__slide" style="background-image: url('{{ asset('storage/' . $slider->image) }}');">
                                <div class="slider__overlay"></div>
                                <div class="container">
                                    <div class="row align-items-center">
                                        <div class="col-lg-8 col-xl-7">
                                            <div class="slider__content">
                                                @if($slider->sub_heading)
                                                    <div class="slider__badge">
                                                        <span class="slider__badge-text">{{ $slider->sub_heading }}</span>
                                                    </div>
                                                @endif
                                                <h1 class="slider__heading">{{ $slider->heading }}</h1>
                                                <div class="slider__actions">
                                                    @php
                                                        $buttons = is_array($slider->buttons) ? $slider->buttons : (is_string($slider->buttons) ? json_decode($slider->buttons, true) : []);
                                                    @endphp
                                                    @if(!empty($buttons) && count($buttons) > 0)
                                                        @if(isset($buttons[0]) && isset($buttons[0]['url']) && isset($buttons[0]['name']))
                                                            <a href="{{ $buttons[0]['url'] }}" class="slider__btn slider__btn--primary">
                                                                <span>{{ $buttons[0]['name'] }}</span>
                                                                <i class="fas fa-arrow-right"></i>
                                                            </a>
                                                        @endif
                                                        @if(isset($buttons[1]) && isset($buttons[1]['url']) && isset($buttons[1]['name']))
                                                            <a href="{{ $buttons[1]['url'] }}" class="slider__btn slider__btn--secondary">
                                                                <span>{{ $buttons[1]['name'] }}</span>
                                                                <i class="fas fa-arrow-right"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="swiper-slide">
                        <div class="slider__slide" style="background-image: url('{{ asset('assets/frontend/images/banner-1.jpg') }}');">
                            <div class="slider__overlay"></div>
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-lg-8 col-xl-7">
                                        <div class="slider__content">
                                            <div class="slider__badge">
                                                <span class="slider__badge-text">Welcome to Paper Wings</span>
                                            </div>
                                            <h1 class="slider__heading">Premium Stationery & Office Supplies</h1>
                                            <div class="slider__actions">
                                                <a href="{{ route('shop') }}" class="slider__btn slider__btn--primary">
                                                    <span>Shop Now</span>
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="swiper-pagination"></div>
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
                    <div class="col-lg-2 col-md-4 col-sm-6 col-6">
                        <div class="category-card">
                            <a href="{{ route('category.show', $category->slug) }}" class="category-card__link">
                                <div class="category-card__image-wrapper">
                                    <div class="image-wrapper skeleton-image-wrapper">
                                        <div class="skeleton-image">
                                            <div class="skeleton-shimmer"></div>
                                        </div>
                                        @php
                                            $categoryImage = !empty($category->thumbnail_url) ? $category->thumbnail_url : asset('assets/images/placeholder.jpg');
                                        @endphp
                                        <img src="{{ $categoryImage }}" alt="{{ $category->name }}" class="category-card__image" loading="lazy" width="300" height="225" onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}';">
                                    </div>
                                    <div class="category-card__overlay"></div>
                                    @if(isset($category->active_products_count) && $category->active_products_count > 0)
                                        <span class="category-card__badge">{{ $category->active_products_count }} items</span>
                                    @endif
                                </div>
                                <div class="category-card__content">
                                    <h3 class="category-card__name">{{ $category->name }}</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
                @endif
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
                <div class="swiper products-carousel">
                    <div class="swiper-wrapper">
                        @foreach($featuredProducts as $featuredProduct)
                            <div class="swiper-slide">
                                @include('frontend.product.partials.product-card', ['product' => $featuredProduct])
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                @else
                    <p class="text-center">No Featured Products Found</p>
                @endif
            </div>

            <!-- On Sale Products Tab -->
            <div class="products__content" id="on-sale">
                @if($onSaleProducts && $onSaleProducts->count() > 0)
                <div class="swiper products-carousel">
                    <div class="swiper-wrapper">
                        @foreach($onSaleProducts as $onSaleProduct)
                            <div class="swiper-slide">
                                @include('frontend.product.partials.product-card', ['product' => $onSaleProduct])
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                @else
                    <p class="text-center">No On Sale Products Found</p>
                @endif
            </div>

            <!-- Top Rated Products Tab -->
            <div class="products__content" id="top-rated">
                @if($topRatedProducts && $topRatedProducts->count() > 0)
                <div class="swiper products-carousel">
                    <div class="swiper-wrapper">
                        @foreach($topRatedProducts as $topRatedProduct)
                            <div class="swiper-slide">
                                @include('frontend.product.partials.product-card', ['product' => $topRatedProduct])
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                @else
                    <p class="text-center">No Top Rated Products Found</p>
                @endif
            </div>
        </div>
    </section>

    <!-- Special Offers Banner Section -->
    @if($specialOfferBanners && $specialOfferBanners->count() > 0)
    <section class="special-offers-banner-section">
        <div class="swiper special-offers-banner-carousel">
            <div class="swiper-wrapper">
                @foreach($specialOfferBanners as $banner)
                <div class="swiper-slide">
                    <div class="special-offers-banner" style="background-image: url('{{ $banner->image_url ?? asset('assets/images/placeholder.jpg') }}');">
                        <div class="floating-bubbles">
                            <span class="bubble"></span>
                            <span class="bubble"></span>
                            <span class="bubble"></span>
                            <span class="bubble"></span>
                            <span class="bubble"></span>
                            <span class="bubble"></span>
                            <span class="bubble"></span>
                            <span class="bubble"></span>
                        </div>
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="special-offers-banner__content">
                                <h2 class="special-offers-banner__title">{{ $banner->title }}</h2>
                                @if($banner->description)
                                    <div class="special-offers-banner__description">{!! $banner->description !!}</div>
                                @endif
                                @if($banner->show_countdown && $banner->end_date)
                                    <div class="countdown-timer" data-end-date="{{ $banner->end_date->format('Y-m-d H:i:s') }}">
                                        <div class="countdown-item">
                                            <span class="countdown-value" data-days>00</span>
                                            <span class="countdown-label">Days</span>
                                        </div>
                                        <div class="countdown-item">
                                            <span class="countdown-value" data-hours>00</span>
                                            <span class="countdown-label">Hours</span>
                                        </div>
                                        <div class="countdown-item">
                                            <span class="countdown-value" data-minutes>00</span>
                                            <span class="countdown-label">Minutes</span>
                                        </div>
                                        <div class="countdown-item">
                                            <span class="countdown-value" data-seconds>00</span>
                                            <span class="countdown-label">Seconds</span>
                                        </div>
                                    </div>
                                @endif
                                @if($banner->button_text && $banner->button_link)
                                    <a href="{{ $banner->button_link }}" class="special-offers-banner__btn">
                                        {{ $banner->button_text }} <i class="fas fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>
    @else
    <section class="special-offers-banner-section offer-coming-soon-section">
        <div class="floating-bubbles">
            <span class="bubble"></span>
            <span class="bubble"></span>
            <span class="bubble"></span>
            <span class="bubble"></span>
            <span class="bubble"></span>
            <span class="bubble"></span>
            <span class="bubble"></span>
            <span class="bubble"></span>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="offer-coming-soon__content">
                        <h2 class="offer-coming-soon__title">Offer Coming Soon</h2>
                        <h3 class="offer-coming-soon__subtitle">Stay Tuned for Exciting Deals</h3>
                        <div class="offer-coming-soon__info">
                            <p>We're preparing something special for you. Check back soon for amazing offers and exclusive discounts!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- New Arrivals Section -->
    <section class="new-arrivals-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="section-header">
                        <h2 class="section-title">New Arrivals</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @if($newArrivals && $newArrivals->count() > 0)
                <div class="swiper new-arrivals-carousel">
                    <div class="swiper-wrapper">
                        @foreach($newArrivals as $product)
                            <div class="swiper-slide">
                                @include('frontend.product.partials.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                @else
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No new arrivals at the moment. Check back soon!</p>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Product Bundles Section -->
    <section class="bundles-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="section-header">
                        <h2 class="section-title">Special Bundles</h2>
                        <p class="section-subtitle">Save more with our exclusive product bundles</p>
                    </div>
                </div>
            </div>
            <div class="row">
                @if($bundles && $bundles->count() > 0)
                <div class="swiper bundles-carousel">
                    <div class="swiper-wrapper">
                        @foreach($bundles as $bundle)
                            <div class="swiper-slide">
                                @include('frontend.product.partials.product-card', ['product' => $bundle])
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
                @else
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No bundles available at the moment. Check back soon!</p>
                </div>
                @endif
            </div>
            @if($bundles && $bundles->count() > 0)
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="{{ route('bundles.index') }}" class="btn btn-outline-primary">View All Bundles</a>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Testimonials Section -->
    @if($testimonials && $testimonials->count() > 0)
    <section class="testimonials-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-header text-center">
                        <h2 class="section-title">What Our Customers Say</h2>
                        <p class="section-subtitle">Read what our satisfied customers have to say about us</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="swiper testimonials-carousel">
                        <div class="swiper-wrapper">
                            @foreach($testimonials as $testimonial)
                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <div class="testimonial-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $testimonial->rating ? 'active' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="testimonial-text">"{{ strip_tags($testimonial->review) }}"</p>
                                    <div class="testimonial-author">
                                        @if($testimonial->image)
                                            <img src="{{ $testimonial->thumbnail_url }}" alt="{{ $testimonial->name }}" class="testimonial-author__image">
                                        @else
                                            <div class="testimonial-author__avatar">{{ substr($testimonial->name, 0, 1) }}</div>
                                        @endif
                                        <div class="testimonial-author__info">
                                            <h4 class="testimonial-author__name">{{ $testimonial->name }}</h4>
                                            @if($testimonial->designation)
                                                <p class="testimonial-author__designation">{{ $testimonial->designation }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

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
                    @php
                        $hasProducts = false;
                        foreach($randomCategories as $category) {
                            if(isset($categoryProducts[$category->id]) && $categoryProducts[$category->id]->count() > 0) {
                                $hasProducts = true;
                                break;
                            }
                        }
                    @endphp
                    @if($hasProducts)
                        @foreach($randomCategories as $category)
                            @if(isset($categoryProducts[$category->id]) && $categoryProducts[$category->id]->count() > 0)
                            <div class="cute-stationery__tab-content {{ $loop->first ? 'active' : '' }}" id="{{ $category->slug }}-content">
                                <div class="swiper cute-stationery-carousel">
                                    <div class="swiper-wrapper">
                                        @foreach($categoryProducts[$category->id] as $product)
                                            <div class="swiper-slide">
                                                @include('frontend.product.partials.product-card', ['product' => $product])
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-pagination"></div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @else
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">No products available in these categories at the moment.</p>
                        </div>
                    @endif
                @else
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No categories available at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- About Section -->
    @if($aboutSection)
    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about__image">
                        <img src="{{ $aboutSection->image_url }}" alt="{{ $aboutSection->title }}" class="about__img">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about__content">
                        @if($aboutSection->badge)
                            <div class="about__badge">{{ $aboutSection->badge }}</div>
                        @endif
                        <h2 class="about__title">{{ $aboutSection->title }}</h2>
                        @if($aboutSection->description)
                            <div class="about__description">{!! $aboutSection->description !!}</div>
                        @endif
                        @if($aboutSection->button_text && $aboutSection->button_link)
                            <a href="{{ $aboutSection->button_link }}" class="about__btn">{{ $aboutSection->button_text }} <i class="fas fa-arrow-right"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

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
                            <form class="subscription-form" id="subscriptionForm" method="POST" action="{{ route('subscription.store') }}">
                                @csrf
                                <div class="subscription-form__input-group">
                                    <input type="email"
                                           name="email"
                                           class="subscription-form__input"
                                           id="subscriptionEmail"
                                           placeholder="Enter Your Email Address"
                                           required
                                           autocomplete="email">
                                    <button type="submit" class="subscription-form__btn" id="subscriptionBtn">
                                        <span class="subscription-btn-text">Subscribe</span>
                                        <span class="subscription-btn-loader" style="display: none;">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                    </button>
                                </div>
                                <div class="subscription-message" id="subscriptionMessage" style="display: none;"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instagram Section -->
    <section class="instagram-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="instagram-section__header text-center">
                        <div class="instagram-section__icon">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <h2 class="instagram-section__title">Follow Us on Instagram</h2>
                        <p class="instagram-section__subtitle">@paperwings</p>
                        @if(isset($instagramLink) && $instagramLink)
                            <a href="{{ $instagramLink }}" target="_blank" rel="noopener noreferrer" class="instagram-section__follow-btn">
                                Follow Us <i class="fas fa-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row g-2">
                @if(isset($instagramPosts) && count($instagramPosts) > 0)
                    <!-- Display real Instagram posts -->
                    @foreach($instagramPosts as $post)
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="instagram-item">
                            <a href="{{ $post['permalink'] ?? ($instagramLink ?? '#') }}" target="_blank" rel="noopener noreferrer" class="instagram-item__link">
                                <div class="instagram-item__image">
                                    <div class="image-wrapper skeleton-image-wrapper">
                                        <div class="skeleton-image">
                                            <div class="skeleton-shimmer"></div>
                                        </div>
                                        <img src="{{ $post['image_url'] }}"
                                             alt="{{ $post['caption'] ?? 'Instagram Post' }}"
                                             class="instagram-item__img"
                                             loading="lazy"
                                             width="200"
                                             height="200"
                                             onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                    </div>
                                    <div class="instagram-item__overlay">
                                        <i class="fab fa-instagram"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endforeach

                    <!-- Fill remaining slots with placeholders if less than 6 posts -->
                    @if(count($instagramPosts) < 6)
                        @for($i = count($instagramPosts) + 1; $i <= 6; $i++)
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="instagram-item">
                                <a href="{{ $instagramLink ?? '#' }}" target="_blank" rel="noopener noreferrer" class="instagram-item__link">
                                    <div class="instagram-item__image">
                                        <div class="image-wrapper skeleton-image-wrapper">
                                            <div class="skeleton-image">
                                                <div class="skeleton-shimmer"></div>
                                            </div>
                                            <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                                 alt="Instagram Post {{ $i }}"
                                                 class="instagram-item__img"
                                                 width="200"
                                                 height="200">
                                        </div>
                                        <div class="instagram-item__overlay">
                                            <i class="fab fa-instagram"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @endfor
                    @endif
                @else
                    <!-- Fallback: Display placeholder images if API is not configured or no posts -->
                    @for($i = 1; $i <= 6; $i++)
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="instagram-item">
                            <a href="{{ $instagramLink ?? '#' }}" target="_blank" rel="noopener noreferrer" class="instagram-item__link">
                                <div class="instagram-item__image">
                                    <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                         alt="Instagram Post {{ $i }}"
                                         class="instagram-item__img">
                                    <div class="instagram-item__overlay">
                                        <i class="fab fa-instagram"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    @endfor
                @endif
            </div>
        </div>
    </section>
@endsection


@push('scripts')
{{-- Home Page JavaScript Modules --}}
<script src="{{ asset('assets/frontend/js/home/countdown.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/home/carousels.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/home/faq.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/home/tabs.js') }}" defer></script>
@endpush

