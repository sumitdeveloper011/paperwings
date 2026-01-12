{{-- Bundle Card Component - Exact same structure as product card --}}
{{-- Usage: @include('frontend.bundle.partials.bundle-card', ['bundle' => $bundle]) --}}

<div class="cute-stationery__item">
    <div class="cute-stationery__image">
        <a href="{{ route('bundle.show', $bundle->slug) }}" class="cute-stationery__image-link">
            <img src="{{ $bundle->thumbnail_url }}" alt="{{ $bundle->name }}" class="cute-stationery__img" loading="lazy">
        </a>

        <!-- Badges Wrapper - Left Side -->
        <div class="product-badges-wrapper">
            <!-- Sale Badge -->
            @if($bundle->discount_percentage)
                <span class="product-badge product-badge--sale">{{ $bundle->discount_percentage }}% OFF</span>
            @endif

            <!-- New Badge -->
            @if($bundle->created_at && $bundle->created_at->isAfter(now()->subDays(30)))
                <span class="product-badge product-badge--new">NEW</span>
            @endif
        </div>

        <!-- Desktop Action Buttons (Hover) -->
        <div class="cute-stationery__actions cute-stationery__actions--desktop">
            <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $bundle->id }}" data-type="bundle" title="Add to Wishlist" aria-label="Add to Wishlist">
                <i class="far fa-heart"></i>
            </button>
            <button class="cute-stationery__action cute-stationery__add-cart bundle-add-to-cart" data-bundle-id="{{ $bundle->id }}" title="Add to Cart" aria-label="Add to Cart">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>
    </div>
    <div class="cute-stationery__info">
        <h3 class="cute-stationery__name">
            <a href="{{ route('bundle.show', $bundle->slug) }}" class="cute-stationery__name-link">
                {{ $bundle->name }}
            </a>
        </h3>

        <!-- Product Count Display (instead of rating) -->
        @if($bundle->bundleProducts && $bundle->bundleProducts->count() > 0)
            <div class="cute-stationery__rating">
                <span class="cute-stationery__rating-text">{{ $bundle->bundleProducts->count() }} {{ $bundle->bundleProducts->count() == 1 ? 'Product' : 'Products' }}</span>
            </div>
        @endif

        <div class="cute-stationery__price">
            @php
                $finalPrice = $bundle->final_price;
                $hasDiscount = $bundle->discount_type !== 'none' && $bundle->discount_price && $bundle->discount_price < $bundle->total_price;
            @endphp
            @if($hasDiscount)
                <span class="cute-stationery__price-old">${{ number_format($bundle->total_price, 2) }}</span>
                <span class="cute-stationery__price-current">${{ number_format($finalPrice, 2) }}</span>
            @else
                <span class="cute-stationery__price-current">${{ number_format($finalPrice, 2) }}</span>
            @endif
        </div>
    </div>

    <!-- Mobile Action Buttons (Always Visible) -->
    <div class="cute-stationery__actions cute-stationery__actions--mobile">
        <button class="cute-stationery__action-mobile wishlist-btn" data-product-id="{{ $bundle->id }}" data-type="bundle" aria-label="Add to Wishlist">
            <i class="far fa-heart"></i>
            <span>Wishlist</span>
        </button>
        <button class="cute-stationery__action-mobile cute-stationery__add-cart-mobile bundle-add-to-cart" data-bundle-id="{{ $bundle->id }}" aria-label="Add to Cart">
            <i class="fas fa-shopping-cart"></i>
            <span>Add to Cart</span>
        </button>
    </div>
</div>
