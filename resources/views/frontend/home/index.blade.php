@extends('layouts.frontend.main')
@section('content')
<!-- Slider Section -->
<section class="slider-section">
        <div class="slider">
            <!-- Slide 1 -->
            <div class="slider__slide" style="background-image: url('{{ asset('assets/frontend/images/banner-1.jpg') }}');">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="slider__content">
                                <div class="slider__tagline">Find The Very Best</div>
                                <h1 class="slider__heading">Stock Up With Our Stationery</h1>
                                <div class="slider__pricing">
                                    <div class="slider__price">Minimum 50 Reams @ $3.75/Ream</div>
                                    <div class="slider__gst">($4.01/Ream Incl. GST)</div>
                                </div>
                                <a href="#" class="slider__btn">
                                    Buy Now <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="slider__slide" style="background-image: url('{{ asset('assets/frontend/images/banner-2.jpg') }}');">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="slider__content">
                                <div class="slider__tagline">Premium Quality</div>
                                <h1 class="slider__heading">Office Supplies & Equipment</h1>
                                <div class="slider__pricing">
                                    <div class="slider__price">Bulk Orders Available</div>
                                    <div class="slider__gst">Corporate Discounts</div>
                                </div>
                                <a href="#" class="slider__btn">
                                    Shop Now <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="slider__slide" style="background-image: url('{{ asset('assets/frontend/images/banner-3.jpg') }}');">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="slider__content">
                                <div class="slider__tagline">Creative Solutions</div>
                                <h1 class="slider__heading">Art & Craft Materials</h1>
                                <div class="slider__pricing">
                                    <div class="slider__price">Professional Grade</div>
                                    <div class="slider__gst">Student & Artist Friendly</div>
                                </div>
                                <a href="#" class="slider__btn">
                                    Explore <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="category-item">
                        <div class="category__image">
                            <img src="{{ asset('assets/frontend/images/stationery.jpg') }}" alt="Books & Stationery" class="category__img">
                        </div>
                        <h3 class="category__name">Books & Stationery</h3>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="category-item">
                        <div class="category__image">
                            <img src="{{ asset('assets/frontend/images/pens.jpg') }}" alt="Pens & Pencils" class="category__img">
                        </div>
                        <h3 class="category__name">Pens & Pencils</h3>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="category-item">
                        <div class="category__image">
                            <img src="{{ asset('assets/frontend/images/paper.jpg') }}" alt="Paper & Card" class="category__img">
                        </div>
                        <h3 class="category__name">Paper & Card</h3>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="category-item">
                        <div class="category__image">
                            <img src="{{ asset('assets/frontend/images/notebooks.jpg') }}" alt="Notebooks" class="category__img">
                        </div>
                        <h3 class="category__name">Notebooks</h3>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="category-item">
                        <div class="category__image">
                            <img src="{{ asset('assets/frontend/images/calendar.jpg') }}" alt="Calendars" class="category__img">
                        </div>
                        <h3 class="category__name">Calendars</h3>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="category-item">
                        <div class="category__image">
                            <img src="{{ asset('assets/frontend/images/school supplies.jpg') }}" alt="School Supplies" class="category__img">
                        </div>
                        <h3 class="category__name">School Supplies</h3>
                    </div>
                </div>
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
                <div class="owl-carousel products-carousel">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Mediocre Iron Shoes" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Mediocre Iron Shoes</h3>
                            <div class="product__price">
                                <span class="product__price-current">$56.22</span>
                                <span class="product__price-old">$76.97</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Ergonomic Aluminum Gloves" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Ergonomic Aluminum Gloves</h3>
                            <div class="product__price">
                                <span class="product__price-current">$73.71</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Durable Paper Lamp" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Durable Paper Lamp</h3>
                            <div class="product__price">
                                <span class="product__price-current">$309.95</span>
                                <span class="product__price-old">$438.75</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Ergonomic Cotton Bench" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Ergonomic Cotton Bench</h3>
                            <div class="product__price">
                                <span class="product__price-current">$749.62</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Mediocre Bronze Shoes" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Mediocre Bronze Shoes</h3>
                            <div class="product__price">
                                <span class="product__price-current">$663.74</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Practical Cotton Knife" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Practical Cotton Knife</h3>
                            <div class="product__price">
                                <span class="product__price-current">$95.00 - $98.87</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- On Sale Products Tab -->
            <div class="products__content" id="on-sale">
                <div class="owl-carousel products-carousel">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Premium Office Chair" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Premium Office Chair</h3>
                            <div class="product__price">
                                <span class="product__price-current">$299.99</span>
                                <span class="product__price-old">$399.99</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Executive Desk Set" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Executive Desk Set</h3>
                            <div class="product__price">
                                <span class="product__price-current">$199.99</span>
                                <span class="product__price-old">$249.99</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Modern Filing Cabinet" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Modern Filing Cabinet</h3>
                            <div class="product__price">
                                <span class="product__price-current">$149.99</span>
                                <span class="product__price-old">$199.99</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Top Rated Products Tab -->
            <div class="products__content" id="top-rated">
                <div class="owl-carousel products-carousel">
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Professional Monitor Stand" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Professional Monitor Stand</h3>
                            <div class="product__price">
                                <span class="product__price-current">$89.99</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Wireless Keyboard" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Wireless Keyboard</h3>
                            <div class="product__price">
                                <span class="product__price-current">$129.99</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-item">
                        <div class="product__image">
                            <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Ergonomic Mouse" class="product__img">
                            <div class="product__actions">
                                <button class="product__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="product__action product__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="product__info">
                            <h3 class="product__name">Ergonomic Mouse</h3>
                            <div class="product__price">
                                <span class="product__price-current">$79.99</span>
                            </div>
                        </div>
                    </div>
                </div>
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
                    <button class="cute-stationery__nav-item active" data-category="deals">DEALS</button>
                    <button class="cute-stationery__nav-item" data-category="journals">JOURNALS</button>
                    <button class="cute-stationery__nav-item" data-category="fancy-pen">FANCY PEN</button>
                    <button class="cute-stationery__nav-item" data-category="backpacks">BACKPACKS</button>
                    <button class="cute-stationery__nav-item" data-category="gift-bags">GIFT BAGS</button>
                </div>
            </div>
            
            <div class="cute-stationery__content">
                <!-- DEALS Tab Content -->
                <div class="cute-stationery__tab-content active" id="deals-content">
                    <div class="owl-carousel cute-stationery-carousel">
                                            <!-- Product 1 -->
                    <div class="cute-stationery__item">
                        <div class="cute-stationery__image">
                            <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Practical Cotton Knife" class="cute-stationery__img">
                            <div class="cute-stationery__actions">
                                <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                            </div>
                        </div>
                        <div class="cute-stationery__info">
                            <h3 class="cute-stationery__name">Practical Cotton Knife</h3>
                            <div class="cute-stationery__price">
                                <span class="cute-stationery__price-current">$95.00-$98.87</span>
                            </div>
                        </div>
                    </div>
                        
                        <!-- Product 2 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Durable Concrete Pants" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Durable Concrete Pants</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$63.51</span>
                                    <span class="cute-stationery__price-old">$380.00</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product 3 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Mediocre Iron Shoes" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Mediocre Iron Shoes</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$56.22</span>
                                    <span class="cute-stationery__price-old">$76.97</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product 4 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Enormous Aluminum Gloves" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Enormous Aluminum Gloves</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$77.73</span>
                                    <span class="cute-stationery__price-old">$866.89</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Product 5 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Durable Paper Lamp" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Durable Paper Lamp</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$309.95</span>
                                    <span class="cute-stationery__price-old">$438.75</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- JOURNALS Tab Content -->
                <div class="cute-stationery__tab-content" id="journals-content">
                    <div class="owl-carousel cute-stationery-carousel">
                        <!-- Journal Product 1 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Premium Leather Journal" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Premium Leather Journal</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$45.99</span>
                                    <span class="cute-stationery__price-old">$59.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Journal Product 2 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Spiral Notebook Set" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Spiral Notebook Set</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$24.50</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Journal Product 3 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Bullet Journal Planner" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Bullet Journal Planner</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$32.99</span>
                                    <span class="cute-stationery__price-old">$39.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Journal Product 4 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Travel Diary" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Travel Diary</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$28.75</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Journal Product 5 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Sketchbook Artist" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Sketchbook Artist</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$18.99</span>
                                    <span class="cute-stationery__price-old">$24.99</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FANCY PEN Tab Content -->
                <div class="cute-stationery__tab-content" id="fancy-pen-content">
                    <div class="owl-carousel cute-stationery-carousel">
                        <!-- Pen Product 1 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Fountain Pen Premium" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Fountain Pen Premium</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$89.99</span>
                                    <span class="cute-stationery__price-old">$129.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pen Product 2 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Gel Pen Collection" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Gel Pen Collection</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$15.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pen Product 3 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Calligraphy Set" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Calligraphy Set</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$67.50</span>
                                    <span class="cute-stationery__price-old">$89.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pen Product 4 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Ballpoint Luxury" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Ballpoint Luxury</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$42.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pen Product 5 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Marker Set" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Marker Set</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$23.75</span>
                                    <span class="cute-stationery__price-old">$29.99</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BACKPACKS Tab Content -->
                <div class="cute-stationery__tab-content" id="backpacks-content">
                    <div class="owl-carousel cute-stationery-carousel">
                        <!-- Backpack Product 1 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="School Backpack" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">School Backpack</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$79.99</span>
                                    <span class="cute-stationery__price-old">$99.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Backpack Product 2 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Laptop Bag" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Laptop Bag</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$129.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Backpack Product 3 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Travel Rucksack" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Travel Rucksack</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$95.50</span>
                                    <span class="cute-stationery__price-old">$119.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Backpack Product 4 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Mini Backpack" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Mini Backpack</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$45.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Backpack Product 5 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Hiking Pack" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Hiking Pack</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$149.99</span>
                                    <span class="cute-stationery__price-old">$189.99</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GIFT BAGS Tab Content -->
                <div class="cute-stationery__tab-content" id="gift-bags-content">
                    <div class="owl-carousel cute-stationery-carousel">
                        <!-- Gift Bag Product 1 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Elegant Gift Bag" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Elegant Gift Bag</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$12.99</span>
                                    <span class="cute-stationery__price-old">$16.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gift Bag Product 2 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Party Favor Bags" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Party Favor Bags</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$8.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gift Bag Product 3 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-2.jpg') }}" alt="Luxury Paper Bag" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Luxury Paper Bag</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$19.99</span>
                                    <span class="cute-stationery__price-old">$24.99</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gift Bag Product 4 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-3.jpg') }}" alt="Reusable Tote" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Reusable Tote</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$15.50</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gift Bag Product 5 -->
                        <div class="cute-stationery__item">
                            <div class="cute-stationery__image">
                                <img src="{{ asset('assets/frontend/images/product-1.jpg') }}" alt="Birthday Bag Set" class="cute-stationery__img">
                                <div class="cute-stationery__actions">
                                    <button class="cute-stationery__action" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
                                    <button class="cute-stationery__action cute-stationery__add-cart" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                </div>
                            </div>
                            <div class="cute-stationery__info">
                                <h3 class="cute-stationery__name">Birthday Bag Set</h3>
                                <div class="cute-stationery__price">
                                    <span class="cute-stationery__price-current">$22.99</span>
                                    <span class="cute-stationery__price-old">$28.99</span>
                                </div>
                            </div>
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