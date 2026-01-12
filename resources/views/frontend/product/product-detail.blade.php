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
  "sku": "{{ $product->barcode ?? $product->id }}",
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

<!-- Product Zoom CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.4/dist/css/lightbox.min.css">
<style>
    .product-main-image img {
        cursor: zoom-in;
        transition: transform 0.3s;
    }
    .product-main-image img:hover {
        transform: scale(1.05);
    }
</style>
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
            <div class="row">
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
                                <a href="{{ $productImages->first()->image_url }}" data-lightbox="product-images" data-title="{{ $product->name }}">
                                    <img src="{{ $productImages->first()->thumbnail_url }}"
                                         alt="{{ $product->name }}"
                                         class="main-img"
                                         id="mainImage"
                                         data-full-image="{{ $productImages->first()->image_url }}"
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'; console.error('Image failed to load: {{ $productImages->first()->thumbnail_url }}');">
                                </a>
                            </div>
                            @if($productImages->count() > 1)
                            <div class="product-thumbnails">
                                @foreach($productImages as $index => $image)
                                    <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                         data-image="{{ $image->image_url }}"
                                         data-thumbnail="{{ $image->thumbnail_url }}">
                                        <a href="{{ $image->image_url }}" data-lightbox="product-images" data-title="{{ $product->name }} - Image {{ $index + 1 }}">
                                            <img src="{{ $image->thumbnail_url }}"
                                                 alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                                 loading="lazy"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'; console.error('Thumbnail failed to load: {{ $image->thumbnail_url }}');">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        @else
                            <!-- Fallback if no images -->
                            <div class="product-main-image">
                                <a href="{{ $mainImageUrl }}" data-lightbox="product-images" data-title="{{ $product->name }}">
                                    <img src="{{ $mainImageUrl }}"
                                         alt="{{ $product->name }}"
                                         class="main-img"
                                         id="mainImage"
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'; console.error('Fallback image failed to load: {{ $mainImageUrl }}');">
                                </a>
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

                        <div class="product-actions">
                            <button class="btn btn-primary add-to-cart" data-product-id="{{ $product->id }}" id="addToCartBtn" {{ !$isInStock ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart"></i>
                                <span class="btn-text">{{ $isInStock ? 'Add to Cart' : 'Out of Stock' }}</span>
                            </button>
                            <button class="btn btn-outline-primary wishlist-btn" data-product-id="{{ $product->id }}">
                                <i class="far fa-heart"></i>
                                Add to Wishlist
                            </button>
                        </div>

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
                                <span class="meta-value">{{ $product->barcode ?? 'N/A' }}</span>
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
                            @if($product->accordions->count() > 0)
                                @foreach($product->accordions as $accordion)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="accordion-{{ $accordion->id }}-tab" data-bs-toggle="tab" data-bs-target="#accordion-{{ $accordion->id }}" type="button" role="tab">{{ $accordion->heading }}</button>
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
                            @endphp
                            @if($hasActiveFaqs)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $product->accordions->count() == 0 ? 'active' : '' }}" id="faqs-tab" data-bs-toggle="tab" data-bs-target="#faqs" type="button" role="tab">FAQs</button>
                                </li>
                            @endif
                            @if($product->description)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $product->accordions->count() == 0 && !$hasActiveFaqs ? 'active' : '' }}" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Product Description</button>
                                </li>
                            @endif
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ ($product->accordions->count() == 0 && !$hasActiveFaqs && !$product->description) ? 'active' : '' }}" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews ({{ $product->reviews_count }})</button>
                            </li>
                            @if($product->approvedQuestions->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions" type="button" role="tab">Q&A</button>
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
                                <div class="tab-pane fade {{ $product->accordions->count() == 0 ? 'show active' : '' }}" id="faqs" role="tabpanel">
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
                                <div class="tab-pane fade {{ $product->accordions->count() == 0 && !$hasActiveFaqs ? 'show active' : '' }}" id="description" role="tabpanel">
                                    <div class="tab-content-body">
                                        <div>{!! $product->description !!}</div>
                                    </div>
                                </div>
                            @endif

                            <div class="tab-pane fade {{ ($product->accordions->count() == 0 && !$hasActiveFaqs && !$product->description) ? 'show active' : '' }}" id="reviews" role="tabpanel">
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
                                            </div>
                                            @guest
                                            <div class="review-form__row">
                                                <div class="review-form__col">
                                                    <label class="review-form__label">Name *</label>
                                                    <input type="text" name="name" class="review-form__input" required>
                                                </div>
                                                <div class="review-form__col">
                                                    <label class="review-form__label">Email *</label>
                                                    <input type="email" name="email" class="review-form__input" required>
                                                </div>
                                            </div>
                                            @endguest
                                            <div class="review-form__field">
                                                <label class="review-form__label">Review *</label>
                                                <textarea name="review" class="review-form__textarea" rows="4" minlength="10" maxlength="1000" required></textarea>
                                                <small class="review-form__hint">Minimum 10 characters, maximum 1000 characters</small>
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
                                            <form id="questionForm">
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
            <div class="row mt-5">
                <div class="col-12">
                    <div class="related-products">
                        <div class="related-products__header text-center">
                            <h2 class="related-products__title">You May Also Like</h2>
                        </div>
                        <div class="row g-4">
                            @if($relatedProducts->count() > 0)
                                @foreach($relatedProducts as $relatedProduct)
                                    <div class="col-lg-3 col-md-4 col-sm-6">
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
{{-- Lightbox2 for Product Zoom - Load before other scripts --}}
<script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.4/dist/js/lightbox.min.js"></script>
<script>
    // Initialize Lightbox after it's loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lightbox !== 'undefined') {
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'fadeDuration': 300,
                'imageFadeDuration': 300
            });
        }
    });
</script>

{{-- Product Page JavaScript Modules --}}
<script src="{{ asset('assets/frontend/js/product/gallery.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/product/quantity.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/product/add-to-cart.js') }}" defer></script>
<script src="{{ asset('assets/frontend/js/product/forms.js') }}" defer></script>

{{-- Product Detail Page Enhancements --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update main image when thumbnail is clicked (use thumbnail for display, full for lightbox)
    document.querySelectorAll('.thumbnail-item').forEach(function(thumbnail) {
        thumbnail.addEventListener('click', function(e) {
            e.preventDefault();
            const fullImage = this.getAttribute('data-image');
            const thumbnailImage = this.getAttribute('data-thumbnail');
            const mainImage = document.getElementById('mainImage');
            const mainImageLink = mainImage ? mainImage.closest('a') : null;

            if (mainImage && fullImage && thumbnailImage) {
                // Add fade effect
                mainImage.style.opacity = '0.5';

                // Update src to thumbnail for fast loading
                setTimeout(function() {
                    mainImage.src = thumbnailImage;
                    mainImage.setAttribute('data-full-image', fullImage);
                    mainImage.classList.add('fade-in');
                    mainImage.style.opacity = '1';

                    // Update lightbox link to full image
                    if (mainImageLink) {
                        mainImageLink.href = fullImage;
                    }
                }, 150);

                // Update active state
                document.querySelectorAll('.thumbnail-item').forEach(function(item) {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            }
        });
    });

    // Copy link functionality
    const copyBtn = document.querySelector('.share-btn--copy');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const url = this.getAttribute('data-copy-url');
            const fullUrl = window.location.origin + url;

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(fullUrl).then(function() {
                    const originalHTML = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                    copyBtn.style.background = '#28a745';
                    setTimeout(function() {
                        copyBtn.innerHTML = originalHTML;
                        copyBtn.style.background = '';
                    }, 2000);
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = fullUrl;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    const originalHTML = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                    copyBtn.style.background = '#28a745';
                    setTimeout(function() {
                        copyBtn.innerHTML = originalHTML;
                        copyBtn.style.background = '';
                    }, 2000);
                } catch (err) {
                    alert('Failed to copy link');
                }
                document.body.removeChild(textArea);
            }
        });
    }
});
</script>

{{-- Pass route URLs to forms module --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reviewForm = document.getElementById('reviewForm');
        const questionForm = document.getElementById('questionForm');

        if (reviewForm) {
            reviewForm.setAttribute('data-review-url', '{{ route("review.store", $product->slug) }}');
        }

        if (questionForm) {
            questionForm.setAttribute('data-question-url', '{{ route("question.store", $product->slug) }}');
        }
    });
</script>
@endpush
@endsection
