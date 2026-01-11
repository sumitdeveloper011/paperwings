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
                                    <img src="{{ $productImages->first()->image_url }}"
                                         alt="{{ $product->name }}"
                                         class="main-img"
                                         id="mainImage"
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'; console.error('Image failed to load: {{ $productImages->first()->image_url }}');">
                                </a>
                            </div>
                            @if($productImages->count() > 1)
                            <div class="product-thumbnails">
                                @foreach($productImages as $index => $image)
                                    <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}"
                                         data-image="{{ $image->image_url }}">
                                        <a href="{{ $image->image_url }}" data-lightbox="product-images" data-title="{{ $product->name }} - Image {{ $index + 1 }}">
                                            <img src="{{ $image->image_url }}"
                                                 alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                                 loading="lazy"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'; console.error('Thumbnail failed to load: {{ $image->image_url }}');">
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
                    <div class="product-info">
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
                                <span class="old-price" style="text-decoration: line-through; color: #999; margin-right: 10px;">${{ number_format($product->total_price, 2) }}</span>
                                <span class="current-price">${{ number_format($product->discount_price, 2) }}</span>
                                <span class="discount">Save {{ round(($product->total_price - $product->discount_price) / $product->total_price * 100) }}%</span>
                            @else
                                <span class="current-price">${{ number_format($product->total_price, 2) }}</span>
                            @endif
                        </div>

                        @if($product->short_description)
                        <div class="product-description mt-3">
                            <p>{!! $product->short_description !!}</p>
                        </div>
                        @endif

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
                            <button class="btn btn-primary add-to-cart" data-product-id="{{ $product->id }}" id="addToCartBtn">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="btn-text">Add to Cart</span>
                            </button>
                            <button class="btn btn-outline-primary add-to-wishlist wishlist-btn" data-product-id="{{ $product->id }}">
                                <i class="fas fa-heart"></i>
                                Add to Wishlist
                            </button>
                        </div>

                        <div class="product-meta">
                            <div class="meta-item">
                                <span class="meta-label">SKU:</span>
                                <span class="meta-value">{{ $product->barcode ?? 'N/A' }}</span>
                            </div>
                            @if($product->category)
                            <div class="meta-item">
                                <span class="meta-label">Category:</span>
                                <span class="meta-value">{{ $product->category->name }}</span>
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
                        <div class="product-tags mt-3">
                            <span class="meta-label">Tags:</span>
                            @foreach($product->tags as $tag)
                                <a href="{{ route('shop') }}?tags[]={{ $tag->id }}" class="tag-link">{{ $tag->name }}</a>
                            @endforeach
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
                                    <h3>Customer Reviews</h3>

                                    @php
                                        $avgRating = $product->average_rating ?? 0;
                                        $reviewsCount = $product->reviews_count ?? 0;
                                        $approvedReviews = $product->approvedReviews;
                                    @endphp

                                    @if($reviewsCount > 0)
                                        <div class="reviews-summary mb-4">
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
                                                <div class="review-item mb-4">
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
                                    <div class="review-form mt-5">
                                        <h4>Write a Review</h4>
                                        <form id="reviewForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Rating *</label>
                                                <div class="star-rating">
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required>
                                                        <label for="rating{{ $i }}" class="star-label"><i class="fas fa-star"></i></label>
                                                    @endfor
                                                </div>
                                            </div>
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
                                                <label class="form-label">Review *</label>
                                                <textarea name="review" class="form-control" rows="5" minlength="10" maxlength="1000" required></textarea>
                                                <small class="text-muted">Minimum 10 characters, maximum 1000 characters</small>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit Review</button>
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
                                        <div class="cute-stationery__item">
                                            <div class="cute-stationery__image">
                                                <a href="{{ route('product.detail', $relatedProduct->slug) }}" class="cute-stationery__image-link">
                                                    <img src="{{ $relatedProduct->main_image }}" alt="{{ $relatedProduct->name }}" class="cute-stationery__img">
                                                </a>
                                                <div class="cute-stationery__actions">
                                                    <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $relatedProduct->id }}" title="Add to Wishlist"><i class="far fa-heart"></i></button>
                                                    <button class="cute-stationery__action cute-stationery__add-cart add-to-cart" data-product-id="{{ $relatedProduct->id }}" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
                                                </div>
                                            </div>
                                            <div class="cute-stationery__info">
                                                <h3 class="cute-stationery__name">
                                                    <a href="{{ route('product.detail', $relatedProduct->slug) }}" class="cute-stationery__name-link">
                                                        {{ $relatedProduct->name }}
                                                    </a>
                                                </h3>
                                                <div class="cute-stationery__price">
                                                    @if($relatedProduct->discount_price)
                                                        <span class="cute-stationery__price-current">${{ $relatedProduct->discount_price }}</span>
                                                        <span class="cute-stationery__price-old">${{ $relatedProduct->total_price }}</span>
                                                    @else
                                                        <span class="cute-stationery__price-current">${{ $relatedProduct->total_price }}</span>
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
