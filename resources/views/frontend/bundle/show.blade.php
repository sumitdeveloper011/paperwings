@extends('layouts.frontend.main')

@push('head')
<!-- Meta Tags -->
<meta name="description" content="{{ $bundle->meta_description ?? strip_tags($bundle->description) }}">
<meta name="keywords" content="{{ $bundle->meta_keywords ?? '' }}">

<!-- Open Graph -->
<meta property="og:title" content="{{ $bundle->name }}">
<meta property="og:description" content="{{ $bundle->meta_description ?? strip_tags($bundle->description) }}">
<meta property="og:image" content="{{ $bundle->image ? asset('storage/' . $bundle->image) : asset('assets/images/placeholder.jpg') }}">
<meta property="og:url" content="{{ route('bundle.show', $bundle->slug) }}">
<meta property="og:type" content="product">
<meta property="og:site_name" content="{{ config('app.name') }}">

<!-- Bundle Specific -->
<meta property="product:price:amount" content="{{ $bundle->total_price }}">
<meta property="product:price:currency" content="NZD">
<meta property="product:availability" content="in stock">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $bundle->name }}">
<meta name="twitter:description" content="{{ $bundle->meta_description ?? strip_tags($bundle->description) }}">
<meta name="twitter:image" content="{{ $bundle->image ? asset('storage/' . $bundle->image) : asset('assets/images/placeholder.jpg') }}">

<!-- Schema.org Structured Data (JSON-LD) -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Product",
  "name": "{{ $bundle->name }}",
  "image": "{{ $bundle->image ? asset('storage/' . $bundle->image) : asset('assets/images/placeholder.jpg') }}",
  "description": "{{ strip_tags($bundle->description ?? '') }}",
  "sku": "{{ $bundle->id }}",
  "offers": {
    "@@type": "Offer",
    "url": "{{ route('bundle.show', $bundle->slug) }}",
    "priceCurrency": "NZD",
    "price": "{{ $bundle->total_price }}",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@@type": "Organization",
      "name": "{{ config('app.name') }}"
    }
  }
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
        'title' => $bundle->name ?? 'Bundle',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Bundles', 'url' => route('bundles.index')],
            ['label' => $bundle->name ?? 'Bundle', 'url' => null]
        ]
    ])

    <section class="product-details-section">
        <div class="container">
            <div class="row">
                <!-- Bundle Images -->
                <div class="col-lg-6">
                    <div class="product-images">
                        @php
                            $bundleImage = $bundle->image ? asset('storage/' . $bundle->image) : asset('assets/images/placeholder.jpg');
                        @endphp

                        <div class="product-main-image">
                            <a href="{{ $bundleImage }}" data-lightbox="bundle-images" data-title="{{ $bundle->name }}">
                                <img src="{{ $bundleImage }}"
                                     alt="{{ $bundle->name }}"
                                     class="main-img"
                                     id="mainImage"
                                     data-full-image="{{ $bundleImage }}"
                                     onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}';">
                            </a>
                            @if($bundle->discount_percentage)
                                <div class="product-badges-wrapper product-badges-wrapper--detail">
                                    <span class="product-badge product-badge--sale">{{ round($bundle->discount_percentage) }}% OFF</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Bundle Info -->
                <div class="col-lg-6">
                    <div class="product-info" id="productInfo">
                        <h1 class="product-title">{{ $bundle->name }}</h1>

                        <div class="product-price">
                            @php
                                $finalPrice = $bundle->final_price;
                                $hasDiscount = $bundle->discount_type !== 'none' && $bundle->discount_price && $bundle->discount_price < $bundle->total_price;
                            @endphp
                            @if($hasDiscount)
                                <span class="old-price">${{ number_format($bundle->total_price, 2) }}</span>
                                <span class="current-price">${{ number_format($finalPrice, 2) }}</span>
                                @if($bundle->discount_type === 'percentage' && $bundle->discount_value)
                                    <span class="discount">Save {{ number_format($bundle->discount_value, 0) }}%</span>
                                @else
                                    <span class="discount">Save ${{ number_format($bundle->total_price - $finalPrice, 2) }}</span>
                                @endif
                            @else
                                <span class="current-price">${{ number_format($finalPrice, 2) }}</span>
                            @endif
                            @if($bundle->bundleProducts->count() > 0)
                                @php
                                    $totalValue = $bundle->bundleProducts->sum(function($product) {
                                        return ($product->final_price ?? $product->total_price) * ($product->pivot->quantity ?? 1);
                                    });
                                    $savings = $totalValue - $finalPrice;
                                @endphp
                                @if($savings > 0 && !$hasDiscount)
                                    <span class="old-price">${{ number_format($totalValue, 2) }}</span>
                                    <span class="discount">Save ${{ number_format($savings, 2) }}</span>
                                @endif
                            @endif
                        </div>

                        <!-- Stock Status - Always In Stock for Bundles -->
                        <div class="product-stock-status">
                            <div class="stock-badge stock-badge--in-stock">
                                <i class="fas fa-check-circle"></i>
                                <span>In Stock</span>
                            </div>
                        </div>

                        @if($bundle->description)
                        <div class="product-description">
                            <p>{!! $bundle->description !!}</p>
                        </div>
                        @endif

                        <div class="product-options">
                            <div class="option-group">
                                <label class="option-label">Quantity:</label>
                                <div class="quantity-selector">
                                    <button class="qty-btn" id="decreaseQty" type="button" aria-label="Decrease quantity">-</button>
                                    <input type="number" value="1" min="1" max="99" id="quantity" class="qty-input" aria-label="Quantity">
                                    <button class="qty-btn" id="increaseQty" type="button" aria-label="Increase quantity">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="product-actions">
                            <button class="btn btn-primary add-to-cart bundle-add-to-cart" data-bundle-id="{{ $bundle->id }}" id="addToCartBtn">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="btn-text">Add Bundle to Cart</span>
                            </button>
                            <button class="btn btn-outline-primary wishlist-btn" data-bundle-id="{{ $bundle->id }}" data-type="bundle">
                                <i class="far fa-heart"></i>
                                Add to Wishlist
                            </button>
                        </div>

                        <!-- Social Sharing -->
                        <div class="product-social-share">
                            <span class="share-label">Share:</span>
                            <div class="share-buttons">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('bundle.show', $bundle->slug)) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="share-btn share-btn--facebook"
                                   title="Share on Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('bundle.show', $bundle->slug)) }}&text={{ urlencode($bundle->name) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="share-btn share-btn--twitter"
                                   title="Share on Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($bundle->name . ' - ' . route('bundle.show', $bundle->slug)) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="share-btn share-btn--whatsapp"
                                   title="Share on WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="mailto:?subject={{ urlencode($bundle->name) }}&body={{ urlencode('Check out this bundle: ' . route('bundle.show', $bundle->slug)) }}"
                                   class="share-btn share-btn--email"
                                   title="Email to a friend">
                                    <i class="fas fa-envelope"></i>
                                </a>
                                <button class="share-btn share-btn--copy"
                                        data-copy-url="{{ route('bundle.show', $bundle->slug) }}"
                                        title="Copy link">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>

                        <div class="product-meta">
                            @if($bundle->bundleProducts->count() > 0)
                            <div class="meta-item">
                                <span class="meta-label">Products:</span>
                                <span class="meta-value">{{ $bundle->bundleProducts->count() }} items</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="product-tabs">
                        <ul class="nav nav-tabs" id="productTabs" role="tablist">
                            @if($bundle->bundleProducts->count() > 0)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="bundle-contents-tab" data-bs-toggle="tab" data-bs-target="#bundle-contents" type="button" role="tab">What's Included in This Bundle</button>
                                </li>
                            @endif
                            @if($bundle->accordions->count() > 0)
                                @foreach($bundle->accordions as $accordion)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="accordion-{{ $accordion->id }}-tab" data-bs-toggle="tab" data-bs-target="#accordion-{{ $accordion->id }}" type="button" role="tab">{{ $accordion->heading }}</button>
                                    </li>
                                @endforeach
                            @endif
                            @if($bundle->description)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ ($bundle->bundleProducts->count() == 0 && $bundle->accordions->count() == 0) ? 'active' : '' }}" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">Bundle Description</button>
                                </li>
                            @endif
                        </ul>

                        <div class="tab-content" id="productTabsContent">
                            @if($bundle->bundleProducts->count() > 0)
                                <div class="tab-pane fade show active" id="bundle-contents" role="tabpanel">
                                    <div class="tab-content-body">
                                        <div class="bundle-products-list">
                                            @foreach($bundle->bundleProducts as $product)
                                                <div class="bundle-product-list-item">
                                                    <div class="bundle-product-list-item__image">
                                                        <a href="{{ route('product.detail', $product->slug) }}">
                                                            <img src="{{ $product->main_thumbnail_url }}" alt="{{ $product->name }}" class="bundle-product-list-item__img">
                                                        </a>
                                                    </div>
                                                    <div class="bundle-product-list-item__info">
                                                        <h4 class="bundle-product-list-item__name">
                                                            <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                                        </h4>
                                                        @if($product->category)
                                                            <div class="bundle-product-list-item__category">
                                                                <a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($bundle->accordions->count() > 0)
                                @foreach($bundle->accordions as $accordion)
                                    <div class="tab-pane fade" id="accordion-{{ $accordion->id }}" role="tabpanel">
                                        <div class="tab-content-body">
                                            <h3>{{ $accordion->heading }}</h3>
                                            {!! $accordion->content !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if($bundle->description)
                                <div class="tab-pane fade {{ ($bundle->bundleProducts->count() == 0 && $bundle->accordions->count() == 0) ? 'show active' : '' }}" id="description" role="tabpanel">
                                    <div class="tab-content-body">
                                        <div>{!! $bundle->description !!}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products (You May Also Like) -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="related-products">
                        <div class="related-products__header text-center">
                            <h2 class="related-products__title">You May Also Like</h2>
                        </div>
                        <div class="row g-4">
                            @if(isset($relatedProducts) && $relatedProducts->count() > 0)
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
<script src="{{ asset('assets/frontend/js/product/quantity.js') }}" defer></script>

{{-- Bundle Detail Page Enhancements --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Bundle Add to Cart
    const bundleAddToCartBtn = document.querySelector('.bundle-add-to-cart');
    if (bundleAddToCartBtn) {
        bundleAddToCartBtn.addEventListener('click', function() {
            const bundleId = this.dataset.bundleId;
            const quantity = document.getElementById('quantity') ? parseInt(document.getElementById('quantity').value) : 1;

            // Add bundle to cart logic here
            // This should call your cart API endpoint
            console.log('Add bundle to cart:', bundleId, 'Quantity:', quantity);
            alert('Bundle add to cart functionality will be implemented');
        });
    }
});
</script>
@endpush
@endsection
