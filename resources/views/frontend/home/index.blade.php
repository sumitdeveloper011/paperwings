@extends('layouts.frontend.main')

@push('head')
@php
    $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
    $siteLogo = !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : asset('assets/frontend/images/logo.png');
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
                        @include('frontend.product.partials.product-card', ['product' => $featuredProduct])
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
                        @include('frontend.product.partials.product-card', ['product' => $onSaleProduct])
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
                        @include('frontend.product.partials.product-card', ['product' => $topRatedProduct])
                    @endforeach
                </div>
                @else
                    <p class="text-center">No Top Rated Products Found</p>
                @endif
            </div>
        </div>
    </section>

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
                <div class="owl-carousel new-arrivals-carousel">
                    @foreach($newArrivals as $product)
                        @include('frontend.product.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                @else
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No new arrivals at the moment. Check back soon!</p>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Recently Viewed Section -->
    <section class="recently-viewed-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="section-header">
                        <h2 class="section-title">Recently Viewed</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @if($recentlyViewed && $recentlyViewed->count() > 0)
                <div class="owl-carousel recently-viewed-carousel">
                    @foreach($recentlyViewed as $product)
                        @include('frontend.product.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                @else
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Start browsing products to see your recently viewed items here!</p>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Special Offers Banner Section -->
    @if($specialOfferBanners && $specialOfferBanners->count() > 0)
    <section class="special-offers-banner-section">
        <div class="container-fluid">
            <div class="owl-carousel special-offers-banner-carousel">
                @foreach($specialOfferBanners as $banner)
                <div class="special-offers-banner" style="background-image: url('{{ $banner->image_url ?? asset('assets/images/placeholder.jpg') }}');">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="special-offers-banner__content">
                                    <h2 class="special-offers-banner__title">{{ $banner->title }}</h2>
                                    @if($banner->description)
                                        <p class="special-offers-banner__description">{{ $banner->description }}</p>
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
                @endforeach
            </div>
        </div>
    </section>
    @endif

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
                            <p class="about__description">{{ $aboutSection->description }}</p>
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
                                <div class="owl-carousel cute-stationery-carousel">
                                    @foreach($categoryProducts[$category->id] as $product)
                                        @include('frontend.product.partials.product-card', ['product' => $product])
                                    @endforeach
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
                <div class="owl-carousel testimonials-carousel">
                    @foreach($testimonials as $testimonial)
                    <div class="testimonial-item">
                        <div class="testimonial-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $testimonial->rating ? 'active' : '' }}"></i>
                            @endfor
                        </div>
                        <p class="testimonial-text">"{{ $testimonial->review }}"</p>
                        <div class="testimonial-author">
                            @if($testimonial->image)
                                <img src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->name }}" class="testimonial-author__image">
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
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- You May Also Like Section -->
    <section class="you-may-also-like-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="section-header">
                        <h2 class="section-title">You May Also Like</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                @if($youMayAlsoLike && $youMayAlsoLike->count() > 0)
                <div class="owl-carousel you-may-also-like-carousel">
                    @foreach($youMayAlsoLike as $product)
                        @include('frontend.product.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                @else
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Browse more products to see personalized recommendations!</p>
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
                <div class="owl-carousel bundles-carousel">
                    @foreach($bundles as $bundle)
                    <div class="bundle-card-home">
                        <div class="bundle-card-home__image">
                            <a href="{{ route('bundle.show', $bundle->slug) }}">
                                <img src="{{ $bundle->image ? asset('storage/' . $bundle->image) : asset('assets/images/placeholder.jpg') }}"
                                     alt="{{ $bundle->name }}"
                                     class="bundle-card-home__img">
                            </a>
                            @if($bundle->discount_percentage)
                            <span class="bundle-badge-home">{{ $bundle->discount_percentage }}% OFF</span>
                            @endif
                        </div>
                        <div class="bundle-card-home__body">
                            <h3 class="bundle-card-home__title">
                                <a href="{{ route('bundle.show', $bundle->slug) }}">{{ $bundle->name }}</a>
                            </h3>
                            <div class="bundle-card-home__price">
                                <span class="bundle-price-home">${{ number_format($bundle->bundle_price, 2) }}</span>
                                @if($bundle->products->count() > 0)
                                <span class="bundle-products-count-home">{{ $bundle->products->count() }} Items</span>
                                @endif
                            </div>
                            <a href="{{ route('bundle.show', $bundle->slug) }}" class="btn btn-sm btn-primary bundle-card-home__btn">View Bundle</a>
                        </div>
                    </div>
                    @endforeach
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

    <!-- FAQ Section -->
    @if($faqs && $faqs->count() > 0)
    <section class="faq-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-header text-center">
                        <h2 class="section-title">Frequently Asked Questions</h2>
                        <p class="section-subtitle">Find answers to common questions</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="faq-accordion">
                        @foreach($faqs as $faq)
                        <div class="faq-item">
                            <div class="faq-question" data-faq-id="{{ $faq->id }}">
                                <h3>{{ $faq->question }}</h3>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ $faq->answer }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Trust Badges Section -->
    <section class="trust-badges-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="trust-badges">
                        <div class="trust-badge-item">
                            <i class="fas fa-shield-alt"></i>
                            <h4>Secure Payment</h4>
                            <p>100% Secure Transactions</p>
                        </div>
                        <div class="trust-badge-item">
                            <i class="fas fa-truck"></i>
                            <h4>Free Shipping</h4>
                            <p>On Orders Over $50</p>
                        </div>
                        <div class="trust-badge-item">
                            <i class="fas fa-undo"></i>
                            <h4>Easy Returns</h4>
                            <p>30-Day Return Policy</p>
                        </div>
                        <div class="trust-badge-item">
                            <i class="fas fa-headset"></i>
                            <h4>24/7 Support</h4>
                            <p>Customer Service</p>
                        </div>
                    </div>
                </div>
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
                                    <img src="{{ $post['image_url'] }}"
                                         alt="{{ $post['caption'] ?? 'Instagram Post' }}"
                                         class="instagram-item__img"
                                         loading="lazy"
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
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

@push('styles')
<style>
/* Alternating Background Colors for Symmetry */
.categories-section {
    background-color: #ffffff !important;
}

.products-section {
    background-color: #f8f9fa !important;
}

.new-arrivals-section {
    background-color: #ffffff !important;
}

.recently-viewed-section {
    background-color: #f8f9fa !important;
}

.cute-stationery-section {
    background-color: #ffffff !important;
}

.testimonials-section {
    background-color: #f8f9fa !important;
}

.you-may-also-like-section {
    background-color: #ffffff !important;
}

.bundles-section {
    background-color: #f8f9fa !important;
}

/* Owl Carousel Navigation Arrows - Fixed Positioning and Styling */
.owl-carousel {
    position: relative;
}

.owl-nav {
    display: block !important;
    position: absolute;
    top: 50%;
    width: 100%;
    transform: translateY(-50%);
    margin: 0;
    z-index: 10;
}

.owl-prev,
.owl-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: #fff !important;
    border: 2px solid #e9ecef !important;
    border-radius: 50%;
    display: flex !important;
    align-items: center;
    justify-content: center;
    font-size: 20px !important;
    color: var(--coral-red) !important;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    z-index: 10;
    margin: 0 !important;
    padding: 0 !important;
}

.owl-prev {
    left: -25px;
}

.owl-next {
    right: -25px;
}

.owl-prev:hover,
.owl-next:hover {
    background: var(--coral-red) !important;
    color: #fff !important;
    border-color: var(--coral-red) !important;
    box-shadow: 0 4px 12px rgba(233, 92, 103, 0.3);
    transform: translateY(-50%) scale(1.1);
}

.owl-prev:active,
.owl-next:active {
    transform: translateY(-50%) scale(0.95);
}

.owl-prev.disabled,
.owl-next.disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.owl-prev span,
.owl-next span {
    display: none;
}

.owl-prev::before {
    content: '\f104';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 20px;
}

.owl-next::before {
    content: '\f105';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 20px;
}

/* Container adjustments for carousel arrows - only for carousel sections */
.new-arrivals-section .container-fluid,
.recently-viewed-section .container-fluid,
.products-section .container-fluid,
.cute-stationery-section .container-fluid,
.you-may-also-like-section .container-fluid,
.bundles-section .container-fluid {
    position: relative;
    padding-left: 60px;
    padding-right: 60px;
}

@media (max-width: 1200px) {
    .new-arrivals-section .container-fluid,
    .recently-viewed-section .container-fluid,
    .products-section .container-fluid,
    .cute-stationery-section .container-fluid,
    .you-may-also-like-section .container-fluid,
    .bundles-section .container-fluid {
        padding-left: 50px;
        padding-right: 50px;
    }
    
    .owl-prev {
        left: -20px;
    }
    
    .owl-next {
        right: -20px;
    }
    
    .owl-prev,
    .owl-next {
        width: 45px;
        height: 45px;
        font-size: 18px !important;
    }
}

@media (max-width: 768px) {
    .new-arrivals-section .container-fluid,
    .recently-viewed-section .container-fluid,
    .products-section .container-fluid,
    .cute-stationery-section .container-fluid,
    .you-may-also-like-section .container-fluid,
    .bundles-section .container-fluid {
        padding-left: 40px;
        padding-right: 40px;
    }
    
    .owl-prev {
        left: -15px;
    }
    
    .owl-next {
        right: -15px;
    }
    
    .owl-prev,
    .owl-next {
        width: 40px;
        height: 40px;
        font-size: 16px !important;
    }
}

@media (max-width: 576px) {
    .new-arrivals-section .container-fluid,
    .recently-viewed-section .container-fluid,
    .products-section .container-fluid,
    .cute-stationery-section .container-fluid,
    .you-may-also-like-section .container-fluid,
    .bundles-section .container-fluid {
        padding-left: 35px;
        padding-right: 35px;
    }
    
    .owl-prev {
        left: -10px;
    }
    
    .owl-next {
        right: -10px;
    }
    
    .owl-prev,
    .owl-next {
        width: 35px;
        height: 35px;
        font-size: 14px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // Wait for jQuery to be available
    (function() {
        function initHomePageScripts() {
            // Check if jQuery is available
            if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
                setTimeout(initHomePageScripts, 100);
                return;
            }

            // Countdown Timer
            $(document).ready(function() {
                $('.countdown-timer').each(function() {
                    const $timer = $(this);
                    const endDate = new Date($timer.data('end-date')).getTime();

                    function updateCountdown() {
                        const now = new Date().getTime();
                        const distance = endDate - now;

                        if (distance < 0) {
                            $timer.html('<div class="countdown-expired">Offer Expired</div>');
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        $timer.find('[data-days]').text(String(days).padStart(2, '0'));
                        $timer.find('[data-hours]').text(String(hours).padStart(2, '0'));
                        $timer.find('[data-minutes]').text(String(minutes).padStart(2, '0'));
                        $timer.find('[data-seconds]').text(String(seconds).padStart(2, '0'));
                    }

                    updateCountdown();
                    setInterval(updateCountdown, 1000);
                });

                // Initialize Special Offers Banner Carousel
                if (typeof jQuery.fn.owlCarousel !== 'undefined' && $('.special-offers-banner-carousel').length) {
                    $('.special-offers-banner-carousel').owlCarousel({
                        loop: true,
                        margin: 0,
                        nav: true,
                        dots: true,
                        autoplay: true,
                        autoplayTimeout: 5000,
                        autoplayHoverPause: true,
                        items: 1,
                        animateOut: 'fadeOut',
                        animateIn: 'fadeIn'
                    });
                }

                // FAQ Accordion
                $('.faq-question').on('click', function() {
                    const $item = $(this).closest('.faq-item');
                    const $answer = $item.find('.faq-answer');

                    // Close other items
                    $('.faq-item').not($item).removeClass('active');

                    // Toggle current item
                    $item.toggleClass('active');
                });

                // Initialize Testimonials Carousel
                if (typeof jQuery.fn.owlCarousel !== 'undefined' && $('.testimonials-carousel').length) {
                    $('.testimonials-carousel').owlCarousel({
                        loop: true,
                        margin: 30,
                        nav: false,
                        dots: true,
                        autoplay: true,
                        autoplayTimeout: 5000,
                        responsive: {
                            0: { items: 1 },
                            768: { items: 2 },
                            992: { items: 3 }
                        }
                    });
                }

                // Initialize You May Also Like Carousel
                if (typeof jQuery.fn.owlCarousel !== 'undefined' && $('.you-may-also-like-carousel').length) {
                    $('.you-may-also-like-carousel').owlCarousel({
                        loop: true,
                        margin: 20,
                        nav: true,
                        dots: false,
                        autoplay: true,
                        autoplayTimeout: 4000,
                        responsive: {
                            0: { items: 1 },
                            576: { items: 2 },
                            768: { items: 3 },
                            992: { items: 4 },
                            1200: { items: 5 }
                        }
                    });
                }

                // Initialize New Arrivals Carousel
                if (typeof jQuery.fn.owlCarousel !== 'undefined' && $('.new-arrivals-carousel').length) {
                    $('.new-arrivals-carousel').owlCarousel({
                        loop: true,
                        margin: 20,
                        nav: true,
                        dots: false,
                        autoplay: true,
                        autoplayTimeout: 4000,
                        responsive: {
                            0: { items: 1 },
                            576: { items: 2 },
                            768: { items: 3 },
                            992: { items: 4 },
                            1200: { items: 5 }
                        }
                    });
                }

                // Initialize Recently Viewed Carousel
                if (typeof jQuery.fn.owlCarousel !== 'undefined' && $('.recently-viewed-carousel').length) {
                    $('.recently-viewed-carousel').owlCarousel({
                        loop: true,
                        margin: 20,
                        nav: true,
                        dots: false,
                        autoplay: true,
                        autoplayTimeout: 4000,
                        responsive: {
                            0: { items: 1 },
                            576: { items: 2 },
                            768: { items: 3 },
                            992: { items: 4 },
                            1200: { items: 5 }
                        }
                    });
                }

                // Initialize Cute Stationery Carousel
                if (typeof jQuery.fn.owlCarousel !== 'undefined' && $('.cute-stationery-carousel').length) {
                    $('.cute-stationery-carousel').owlCarousel({
                        loop: true,
                        margin: 20,
                        nav: true,
                        dots: false,
                        autoplay: true,
                        autoplayTimeout: 4000,
                        responsive: {
                            0: { items: 1 },
                            576: { items: 2 },
                            768: { items: 3 },
                            992: { items: 4 },
                            1200: { items: 5 }
                        }
                    });
                }

                // Initialize Bundles Carousel
                if (typeof jQuery.fn.owlCarousel !== 'undefined' && $('.bundles-carousel').length) {
                    $('.bundles-carousel').owlCarousel({
                        loop: true,
                        margin: 20,
                        nav: true,
                        dots: false,
                        autoplay: true,
                        autoplayTimeout: 4000,
                        responsive: {
                            0: { items: 1 },
                            576: { items: 2 },
                            768: { items: 3 },
                            992: { items: 4 }
                        }
                    });
                }

                // Cute Stationery Tab Navigation
                $('.cute-stationery__nav-item').on('click', function() {
                    const categorySlug = $(this).data('category');
                    $('.cute-stationery__nav-item').removeClass('active');
                    $(this).addClass('active');
                    $('.cute-stationery__tab-content').removeClass('active');
                    $('#' + categorySlug + '-content').addClass('active');
                });
            });
        }

        // Start initialization
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHomePageScripts);
        } else {
            initHomePageScripts();
        }
    })();
</script>
@endpush

