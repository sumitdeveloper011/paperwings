@extends('layouts.frontend.main')

@push('head')
<!-- Meta Tags -->
<meta name="description" content="{{ $product->meta_description ?? $product->short_description ?? strip_tags($product->description) }}">
<meta name="keywords" content="{{ $product->meta_keywords ?? $product->tags->pluck('name')->implode(', ') }}">

<!-- Open Graph -->
<meta property="og:title" content="{{ $product->meta_title ?? $product->name }}">
<meta property="og:description" content="{{ $product->meta_description ?? $product->short_description ?? strip_tags($product->description) }}">
<meta property="og:image" content="{{ $product->main_image }}">
<meta property="og:url" content="{{ route('product.detail', $product->slug) }}">
<meta property="og:type" content="product">
<meta property="og:site_name" content="{{ config('app.name') }}">

<!-- Product Specific -->
<meta property="product:price:amount" content="{{ $product->discount_price ?? $product->total_price }}">
<meta property="product:price:currency" content="NZD">
<meta property="product:availability" content="in stock">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $product->meta_title ?? $product->name }}">
<meta name="twitter:description" content="{{ $product->meta_description ?? $product->short_description ?? strip_tags($product->description) }}">
<meta name="twitter:image" content="{{ $product->main_image }}">

<!-- Schema.org Structured Data (JSON-LD) -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Product",
  "name": "{{ $product->name }}",
  "image": "{{ $product->main_image }}",
  "description": "{{ strip_tags($product->description ?? $product->short_description) }}",
  "sku": "{{ $product->sku ?? ($product->barcode ?? $product->id) }}",
  "brand": {
    "@@type": "Brand",
    "name": "{{ $product->brand->name ?? 'Paper Wings' }}"
  },
  "offers": {
    "@@type": "Offer",
    "url": "{{ route('product.detail', $product->slug) }}",
    "priceCurrency": "NZD",
    "price": "{{ $product->discount_price ?? $product->total_price }}",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@@type": "Organization",
      "name": "{{ config('app.name') }}"
    }
  }@if($product->reviews_count > 0),
  "aggregateRating": {
    "@@type": "AggregateRating",
    "ratingValue": "{{ $product->average_rating }}",
    "reviewCount": "{{ $product->reviews_count }}"
  }@endif
}
</script>

@endpush

@section('content')
    @include('frontend.partials.page-header', [
        'title' => $product->name ?? 'Product',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => $product->category->name ?? 'Products', 'url' => $product->category ? route('category.show', $product->category->slug) : route('shop')],
            ['label' => $product->name ?? 'Product', 'url' => null]
        ]
    ])

    <section class="product-details-section">
        <div class="container">
            <div class="row g-4">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-images">
                        @php
                            // Ensure images relationship is loaded
                            if (!$product->relationLoaded('images')) {
                                $product->load('images');
                            }
                            // Get unique images by image path to prevent duplicates
                            $productImages = $product->images->unique('image');
                            $hasImages = $productImages && $productImages->count() > 0;
                            $mainImageUrl = $hasImages ? $productImages->first()->image_url : ($product->main_image ?? asset('assets/images/placeholder.jpg'));
                        @endphp

                        @if($hasImages)
                            <div class="product-main-image">
                                <div class="image-wrapper skeleton-image-wrapper product-image-clickable" data-image-index="0">
                                    <div class="skeleton-main-image">
                                        <div class="skeleton-shimmer"></div>
                                    </div>
                                    <img src="{{ $productImages->first()->medium_url ?? $productImages->first()->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="main-img"
                                         id="mainImage"
                                         data-full-image="{{ $productImages->first()->image_url }}"
                                         data-medium-image="{{ $productImages->first()->medium_url ?? $productImages->first()->image_url }}"
                                         width="600"
                                         height="600"
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}';">
                                </div>
                            </div>
                            @if($productImages->count() > 1)
                            <div class="product-thumbnails">
                                @foreach($productImages as $index => $image)
                                    <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                         data-image="{{ $image->image_url }}"
                                         data-medium-image="{{ $image->medium_url ?? $image->image_url }}"
                                         data-thumbnail="{{ $image->thumbnail_url }}"
                                         data-image-index="{{ $index }}">
                                        <div class="image-wrapper skeleton-image-wrapper">
                                            <div class="skeleton-thumbnail">
                                                <div class="skeleton-shimmer"></div>
                                            </div>
                                            <img src="{{ $image->thumbnail_url }}"
                                                 alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                                 loading="lazy"
                                                 width="80"
                                                 height="80"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}';">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        @else
                            <!-- Fallback if no images -->
                            <div class="product-main-image">
                                <div class="image-wrapper skeleton-image-wrapper product-image-clickable" data-image-index="0">
                                    <div class="skeleton-main-image">
                                        <div class="skeleton-shimmer"></div>
                                    </div>
                                    <img src="{{ $mainImageUrl }}"
                                         alt="{{ $product->name }}"
                                         class="main-img"
                                         id="mainImage"
                                         loading="lazy"
                                         width="600"
                                         height="600"
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}';">
                                </div>
                            </div>
                        @endif
                        
                        {{-- Lightbox HTML structure for product images --}}
                        @if($hasImages && $productImages->count() > 0)
                        <div class="lightbox" id="lightbox">
                            <button class="lightbox__close" aria-label="Close lightbox">
                                <i class="fas fa-times"></i>
                            </button>
                            <button class="lightbox__prev" aria-label="Previous image">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="lightbox__next" aria-label="Next image">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <div class="lightbox__content">
                                <img src="" alt="" id="lightbox-image">
                                <div class="lightbox__caption" id="lightbox-caption"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info" id="productInfo">
                        <h1 class="product-title">{{ $product->name }}</h1>
                        <div class="product-rating">
                            @php
                                $avgRating = $product->average_rating ?? 0;
                                $reviewsCount = $product->reviews_count ?? 0;
                            @endphp
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($avgRating))
                                        <i class="fas fa-star active"></i>
                                    @elseif($i - 0.5 <= $avgRating)
                                        <i class="fas fa-star-half-alt active"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="rating-text">{{ number_format($avgRating, 1) }} out of 5</span>
                            <span class="reviews-count">({{ $reviewsCount }} {{ Str::plural('review', $reviewsCount) }})</span>
                        </div>

                        <div class="product-price">
                            @if($product->discount_price)
                                <span class="old-price">${{ number_format($product->total_price, 2) }}</span>
                                <span class="current-price">${{ number_format($product->discount_price, 2) }}</span>
                                <span class="discount">Save {{ round(($product->total_price - $product->discount_price) / $product->total_price * 100) }}%</span>
                            @else
                                <span class="current-price">${{ number_format($product->total_price, 2) }}</span>
                            @endif
                        </div>

                        <!-- Stock Status -->
                        @php
                            $stock = $product->stock ?? 0;
                            $isInStock = $stock > 0;
                            $isLowStock = $stock > 0 && $stock <= 10;
                        @endphp
                        <div class="product-stock-status">
                            @if($isInStock)
                                @if($isLowStock)
                                    <div class="stock-badge stock-badge--low">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Only {{ $stock }} left in stock!</span>
                                    </div>
                                @else
                                    <div class="stock-badge stock-badge--in-stock">
                                        <i class="fas fa-check-circle"></i>
                                        <span>In Stock</span>
                                    </div>
                                @endif
                            @else
                                <div class="stock-badge stock-badge--out-of-stock">
                                    <i class="fas fa-times-circle"></i>
                                    <span>Out of Stock</span>
                                </div>
                            @endif
                        </div>

                        @if($product->short_description)
                        <div class="product-description">
                            <p>{!! $product->short_description !!}</p>
                        </div>
                        @endif

                        <div class="product-options">
                            <div class="option-group">
                                <label class="option-label">Quantity:</label>
                                <div class="quantity-selector">
                                    <button class="qty-btn" id="decreaseQty" type="button" aria-label="Decrease quantity">-</button>
                                    <input type="number" value="1" min="1" max="{{ $isInStock ? min($stock, 99) : 99 }}" id="quantity" class="qty-input" aria-label="Quantity">
                                    <button class="qty-btn" id="increaseQty" type="button" aria-label="Increase quantity">+</button>
                                </div>
                                @if($isInStock && $stock < 99)
                                    <small class="quantity-hint">Max: {{ $stock }} available</small>
                                @endif
                            </div>
                        </div>

                        @if(!empty($product->uuid))
                        <div class="product-actions">
                            <button class="btn btn-primary add-to-cart" data-product-uuid="{{ $product->uuid }}" id="addToCartBtn" {{ !$isInStock ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart"></i>
                                <span class="btn-text">{{ $isInStock ? 'Add to Cart' : 'Out of Stock' }}</span>
                            </button>
                            <button class="btn btn-outline-primary wishlist-btn" data-product-uuid="{{ $product->uuid }}">
                                <i class="far fa-heart"></i>
                                Add to Wishlist
                            </button>
                        </div>
                        @endif

                        <!-- Social Sharing -->
                        <div class="product-social-share">
                            <span class="share-label">Share:</span>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('product.detail', $product->slug)) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="share-btn share-btn--facebook"
                                   title="Share on Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('product.detail', $product->slug)) }}&text={{ urlencode($product->name) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="share-btn share-btn--twitter"
                                   title="Share on Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . route('product.detail', $product->slug)) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="share-btn share-btn--whatsapp"
                                   title="Share on WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="mailto:?subject={{ urlencode($product->name) }}&body={{ urlencode('Check out this product: ' . route('product.detail', $product->slug)) }}"
                                   class="share-btn share-btn--email"
                                   title="Email to a friend">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                <button class="share-btn share-btn--copy"
                                        data-copy-url="{{ route('product.detail', $product->slug) }}"
                                        title="Copy link">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>

                        <div class="product-meta">
                            <div class="meta-item">
                                <span class="meta-label">SKU:</span>
                                <span class="meta-value">{{ $product->sku ?? ($product->barcode ?? 'N/A') }}</span>
                            </div>
                            @if($product->category)
                            <div class="meta-item">
                                <span class="meta-label">Category:</span>
                                <a href="{{ route('category.show', $product->category->slug) }}" class="meta-value meta-link">{{ $product->category->name }}</a>
                            </div>
                            @endif
                            @if($product->brand)
                            <div class="meta-item">
                                <span class="meta-label">Brand:</span>
                                <span class="meta-value">{{ $product->brand->name }}</span>
                            </div>
                            @endif
                        </div>

                        <!-- Product Tags -->
                        @if($product->tags && $product->tags->count() > 0)
                        <div class="product-tags">
                            <span class="meta-label">Tags:</span>
                            <div class="tags-list">
                                @foreach($product->tags as $tag)
                                    <a href="{{ route('shop') }}?tags[]={{ $tag->id }}" class="tag-link">{{ $tag->name }}</a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="product-tabs">
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            @if($product->product_type == 4 && $product->bundleProducts && $product->bundleProducts->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="bundle-contents-tab" data-bs-toggle="tab" data-bs-target="#bundle-contents" type="button" role="tab">What's Included in This Bundle</button>
                                </li>
                            @endif
                            @if($product->accordions->count() > 0)
                                @foreach($product->accordions as $accordion)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ ($product->product_type == 4 && $product->bundleProducts && $product->bundleProducts->count() > 0) ? '' : ($loop->first ? 'active' : '') }}" id="accordion-{{ $accordion->id }}-tab" data-bs-toggle="tab" data-bs-target="#accordion-{{ $accordion->id }}" type="button" role="tab">{{ $accordion->heading }}</button>
                                    </li>
                                @endforeach
                            @endif
                            @php
                                $hasActiveFaqs = false;
                                foreach($product->activeFaqs as $productFaq) {
                                    if ($productFaq->faqs && is_array($productFaq->faqs)) {
                                        foreach($productFaq->faqs as $faqItem) {
                                            if (isset($faqItem['status']) && $faqItem['status']) {
                                                $hasActiveFaqs = true;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                                $hasBundleContents = $product->product_type == 4 && $product->bundleProducts && $product->bundleProducts->count() > 0;
                                $firstTabActive = !$hasBundleContents && $product->accordions->count() == 0 && !$hasActiveFaqs && !$product->description;
                            @endphp
                            @if($hasActiveFaqs)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $firstTabActive ? 'active' : '' }}" id="faqs-tab" data-bs-toggle="tab" data-bs-target="#faqs" type="button" role="tab">FAQs</button>
                                </li>
                            @endif
                            @if($product->description)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ ($product->accordions->count() == 0 && !$hasActiveFaqs && !$hasBundleContents) ? 'active' : '' }}" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Product Description</button>
                                </li>
                            @endif
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $firstTabActive ? 'active' : '' }}" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews ({{ $product->reviews_count }})</button>
                            </li>
                            @if($product->approvedQuestions->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions" type="button" role="tab">Q&A</button>
                                </li>
                            @endif
                        </ul>

                        <div class="tab-content" id="productTabsContent">
                            @if($product->product_type == 4 && $product->bundleProducts && $product->bundleProducts->count() > 0)
                                <div class="tab-pane fade show active" id="bundle-contents" role="tabpanel">
                                    <div class="tab-content-body">
                                        <div class="bundle-products-list">
                                            @foreach($product->bundleProducts as $bundleProduct)
                                                <div class="bundle-product-list-item">
                                                    <div class="bundle-product-list-item__image">
                                                        <a href="{{ route('product.detail', $bundleProduct->slug) }}">
                                                            <img src="{{ $bundleProduct->main_thumbnail_url ?? asset('assets/images/placeholder.jpg') }}" alt="{{ $bundleProduct->name }}" class="bundle-product-list-item__img">
                                                        </a>
                                                    </div>
                                                    <div class="bundle-product-list-item__info">
                                                        <h4 class="bundle-product-list-item__name">
                                                            <a href="{{ route('product.detail', $bundleProduct->slug) }}">{{ $bundleProduct->name }}</a>
                                                        </h4>
                                                        @if($bundleProduct->category)
                                                            <div class="bundle-product-list-item__category">
                                                                <a href="{{ route('category.show', $bundleProduct->category->slug) }}">{{ $bundleProduct->category->name }}</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($product->accordions->count() > 0)
                                @foreach($product->accordions as $accordion)
                                    <div class="tab-pane fade {{ (!$hasBundleContents && $loop->first) ? 'show active' : '' }}" id="accordion-{{ $accordion->id }}" role="tabpanel">
                                        <div class="tab-content-body">
                                            <h3>{{ $accordion->heading }}</h3>
                                            {!! $accordion->content !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @php
                                $activeFaqsList = collect();
                                foreach($product->activeFaqs as $productFaq) {
                                    if ($productFaq->faqs && is_array($productFaq->faqs)) {
                                        foreach($productFaq->faqs as $index => $faqItem) {
                                            if (isset($faqItem['status']) && $faqItem['status']) {
                                                $activeFaqsList->push([
                                                    'id' => $productFaq->id . '_' . $index,
                                                    'question' => $faqItem['question'] ?? '',
                                                    'answer' => $faqItem['answer'] ?? '',
                                                    'sort_order' => $faqItem['sort_order'] ?? 999
                                                ]);
                                            }
                                        }
                                    }
                                }
                                $activeFaqsList = $activeFaqsList->sortBy('sort_order');
                            @endphp
                            @if($activeFaqsList->count() > 0)
                                <div class="tab-pane fade {{ $firstTabActive ? 'show active' : '' }}" id="faqs" role="tabpanel">
                                    <div class="tab-content-body">
                                        <h3>Frequently Asked Questions</h3>
                                        <div class="accordion" id="productFaqAccordion">
                                            @foreach($activeFaqsList as $faq)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="faqHeading{{ $faq['id'] }}">
                                                        <button class="accordion-button {{ !$loop->first ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $faq['id'] }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                                            {{ $faq['question'] }}
                                                        </button>
                                                    </h2>
                                                    <div id="faqCollapse{{ $faq['id'] }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="faqHeading{{ $faq['id'] }}">
                                                        <div class="accordion-body">
                                                            {!! $faq['answer'] !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($product->description)
                                <div class="tab-pane fade {{ (!$hasBundleContents && $product->accordions->count() == 0 && !$hasActiveFaqs) ? 'show active' : '' }}" id="description" role="tabpanel">
                                    <div class="tab-content-body">
                                        <div>{!! $product->description !!}</div>
                                    </div>
                                </div>
                            @endif

                            <div class="tab-pane fade {{ $firstTabActive ? 'show active' : '' }}" id="reviews" role="tabpanel">
                                <div class="tab-content-body">
                                    <h3 class="reviews-section-title">Customer Reviews</h3>

                                    @php
                                        $avgRating = $product->average_rating ?? 0;
                                        $reviewsCount = $product->reviews_count ?? 0;
                                        $approvedReviews = $product->approvedReviews;
                                    @endphp

                                    @if($reviewsCount > 0)
                                        <div class="reviews-summary mb-3">
                                            <div class="overall-rating">
                                                <div class="rating-number">{{ number_format($avgRating, 1) }}</div>
                                                <div class="rating-stars">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= floor($avgRating))
                                                            <i class="fas fa-star active"></i>
                                                        @elseif($i - 0.5 <= $avgRating)
                                                            <i class="fas fa-star-half-alt active"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <div class="total-reviews">Based on {{ $reviewsCount }} {{ Str::plural('review', $reviewsCount) }}</div>
                                            </div>
                                        </div>

                                        <div class="reviews-list">
                                            @foreach($approvedReviews as $review)
                                                <div class="review-item mb-3">
                                                    <div class="review-header d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <div class="reviewer-name">{{ $review->reviewer_name }}</div>
                                                            @if($review->verified_purchase)
                                                                <span class="badge bg-success">Verified Purchase</span>
                                                            @endif
                                                        </div>
                                                        <div class="review-rating">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star {{ $i <= $review->rating ? 'active' : '' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <div class="review-date">{{ $review->created_at->format('M d, Y') }}</div>
                                                    </div>
                                                    <div class="review-content">
                                                        <p>{{ $review->review }}</p>
                                                    </div>
                                                    @if($review->helpful_count > 0)
                                                        <div class="review-helpful">
                                                            <small>{{ $review->helpful_count }} people found this helpful</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                                    @endif

                                    <!-- Review Form -->
                                    <div class="review-form">
                                        <h4 class="review-form__title">Write a Review</h4>
                                        <form id="reviewForm" class="review-form__form">
                                            @csrf
                                            <div class="review-form__field">
                                                <label class="review-form__label">Rating *</label>
                                                <div class="star-rating">
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required>
                                                        <label for="rating{{ $i }}" class="star-label"><i class="fas fa-star"></i></label>
                                                    @endfor
                                                </div>
                                                <div class="invalid-feedback invalid-feedback--review"></div>
                                            </div>
                                            @guest
                                            <div class="review-form__row">
                                                <div class="review-form__col">
                                                    <label class="review-form__label">Name *</label>
                                                    <input type="text" name="name" class="review-form__input" minlength="2" maxlength="255" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="review-form__col">
                                                    <label class="review-form__label">Email *</label>
                                                    <input type="email" name="email" class="review-form__input" maxlength="255" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            @endguest
                                            <div class="review-form__field">
                                                <label class="review-form__label">Review *</label>
                                                <textarea name="review" class="review-form__textarea" rows="2" minlength="10" maxlength="1000" required></textarea>
                                                <small class="review-form__hint">Minimum 10 characters, maximum 1000 characters</small>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <button type="submit" class="review-form__submit btn btn-primary">Submit Review</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @if($product->approvedQuestions->count() > 0)
                                <div class="tab-pane fade" id="questions" role="tabpanel">
                                    <div class="tab-content-body">
                                        <h3>Questions & Answers</h3>
                                        <div class="questions-list">
                                            @foreach($product->approvedQuestions as $question)
                                                <div class="question-item mb-4">
                                                    <div class="question-header mb-2">
                                                        <strong>Q: {{ $question->question }}</strong>
                                                        <small class="text-muted d-block">Asked by {{ $question->reviewer_name }} on {{ $question->created_at->format('M d, Y') }}</small>
                                                    </div>
                                                    @if($question->approvedAnswers->count() > 0)
                                                        <div class="answers-list ms-4">
                                                            @foreach($question->approvedAnswers->sortByDesc('helpful_count') as $answer)
                                                                <div class="answer-item mb-3">
                                                                    <div class="answer-header d-flex justify-content-between align-items-start mb-2">
                                                                        <div>
                                                                            <strong>A: </strong>{{ $answer->answer }}
                                                                            <small class="text-muted d-block">Answered by {{ $answer->reviewer_name ?? $answer->name }} on {{ $answer->created_at->format('M d, Y') }}</small>
                                                                        </div>
                                                                        <button class="btn btn-sm btn-outline-primary helpful-btn" data-answer-id="{{ $answer->id }}">
                                                                            <i class="fas fa-thumbs-up"></i> Helpful ({{ $answer->helpful_count }})
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    <div class="answer-form ms-4 mt-2">
                                                        <form class="answer-form-inline" data-question-id="{{ $question->id }}">
                                                            @csrf
                                                            <div class="input-group">
                                                                <input type="text" name="answer" class="form-control" placeholder="Your answer..." required>
                                                                @guest
                                                                <input type="text" name="name" class="form-control" placeholder="Your name" required>
                                                                @endguest
                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Ask Question Form -->
                                        <div class="question-form mt-5">
                                            <h4>Ask a Question</h4>
                                            <form id="questionForm" action="{{ route('question.store', $product->slug) }}" method="POST">
                                                @csrf
                                                @guest
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Name *</label>
                                                        <input type="text" name="name" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Email *</label>
                                                        <input type="email" name="email" class="form-control" required>
                                                    </div>
                                                </div>
                                                @endguest
                                                <div class="mb-3">
                                                    <label class="form-label">Your Question *</label>
                                                    <textarea name="question" class="form-control" rows="3" minlength="10" maxlength="500" required></textarea>
                                                    <small class="text-muted">Minimum 10 characters, maximum 500 characters</small>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Ask Question</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            <div class="row mt-5 mb-4">
                <div class="col-12">
                    <div class="related-products">
                        <div class="related-products__header text-center">
                            <h2 class="related-products__title">You May Also Like</h2>
                        </div>
                        <div class="row g-4">
                            @if($relatedProducts->count() > 0)
                                @foreach($relatedProducts as $relatedProduct)
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                        @include('frontend.product.partials.product-card', ['product' => $relatedProduct])
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-center text-muted">No related products found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@push('scripts')
{{-- Gallery Lightbox Library --}}
<script src="{{ asset('assets/frontend/js/gallery-lightbox.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>

{{-- Analytics: Pass product data to JavaScript --}}
@php
    $productData = [
        'id' => $product->id,
        'name' => $product->name,
        'category' => $product->category->name ?? 'Uncategorized',
        'brand' => $product->brand->name ?? '',
        'price' => $product->discount_price ?? $product->total_price ?? 0
    ];
@endphp
<script>
    window.ProductAnalyticsData = @json($productData);
</script>

{{-- Initialize GalleryLightbox with product images --}}
@if($hasImages && $productImages->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set placeholder image path for GalleryLightbox
        window.PLACEHOLDER_IMAGE = '{{ asset('assets/images/placeholder.jpg') }}';
        
        @php
            $placeholderUrl = asset('assets/images/placeholder.jpg');
            $imageItems = $productImages->filter(function($image) {
                return $image && $image->image_url && $image->image_url !== '';
            })->map(function($image, $index) use ($product, $placeholderUrl) {
                // Ensure we have a valid image URL, fallback to placeholder if needed
                $imageUrl = $image->image_url ?? $placeholderUrl;
                return [
                    'image' => $imageUrl,
                    'title' => $index === 0 ? $product->name : $product->name . ' - Image ' . ($index + 1),
                    'description' => null
                ];
            })->values()->all();
        @endphp
        const productImageItems = @json($imageItems);

        if (productImageItems.length > 0 && typeof GalleryLightbox !== 'undefined') {
            GalleryLightbox.init(productImageItems);

            // Add click handlers to main image and thumbnails
            const mainImageWrapper = document.querySelector('.product-image-clickable');
            if (mainImageWrapper) {
                mainImageWrapper.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-image-index')) || 0;
                    GalleryLightbox.open(index);
                });
            }

            document.querySelectorAll('.thumbnail-item').forEach(function(thumbnail) {
                thumbnail.addEventListener('click', function(e) {
                    // Don't open lightbox if clicking on thumbnail (let gallery.js handle image switching)
                    // Only open lightbox if double-clicking or if there's a specific lightbox trigger
                    const imageIndex = parseInt(this.getAttribute('data-image-index')) || 0;
                    const mainImageWrapper = document.querySelector('.product-image-clickable');
                    if (mainImageWrapper) {
                        mainImageWrapper.setAttribute('data-image-index', imageIndex);
                    }
                });
            });

            // Open lightbox when clicking on main image
            const mainImage = document.getElementById('mainImage');
            if (mainImage) {
                mainImage.style.cursor = 'zoom-in';
                mainImage.addEventListener('click', function() {
                    const mainImageWrapper = document.querySelector('.product-image-clickable');
                    const index = mainImageWrapper ? parseInt(mainImageWrapper.getAttribute('data-image-index')) || 0 : 0;
                    GalleryLightbox.open(index);
                });
            }
        }
    });
</script>
@endif

{{-- Form URLs: Pass route URLs to JavaScript via data attributes --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reviewForm = document.getElementById('reviewForm');
        const questionForm = document.getElementById('questionForm');

        if (reviewForm) {
            reviewForm.dataset.reviewUrl = '{{ route("review.store", $product->slug) }}';
        }

        if (questionForm) {
            questionForm.dataset.questionUrl = '{{ route("question.store", $product->slug) }}';
        }
    });
</script>

{{-- Product Page JavaScript Modules --}}
<script src="{{ asset('assets/frontend/js/product/gallery.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/product/quantity.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/product/forms.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
<script src="{{ asset('assets/frontend/js/product/product-detail.js') }}?v={{ config('app.asset_version', '1.0.0') }}" defer></script>
@endpush
@endsection