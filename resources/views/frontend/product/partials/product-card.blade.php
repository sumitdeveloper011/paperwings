<div class="cute-stationery__item">
    <div class="cute-stationery__image">
        <a href="{{ route('product.detail', $product->slug) }}" class="cute-stationery__image-link">
            @php
                $imageUrl = $product->main_thumbnail_url ?? asset('assets/images/placeholder.jpg');
                $placeholderUrl = asset('assets/images/placeholder.jpg');
            @endphp
            <div class="image-wrapper skeleton-image-wrapper">
                <div class="skeleton-image">
                    <div class="skeleton-shimmer"></div>
                </div>
                <img src="{{ $imageUrl }}" 
                     alt="{{ $product->name }}" 
                     class="cute-stationery__img" 
                     loading="lazy" 
                     width="400"
                     height="300"
                     onerror="this.onerror=null; this.src='{{ $placeholderUrl }}';">
            </div>
        </a>

        <!-- Badges Wrapper - Left Side -->
        <div class="product-badges-wrapper">
            <!-- Sale Badge -->
            @if($product->discount_price)
                @php
                    $discountPercent = round((($product->total_price - $product->discount_price) / $product->total_price) * 100);
                @endphp
                <span class="product-badge product-badge--sale">{{ $discountPercent }}% OFF</span>
            @endif

            <!-- New Badge -->
            @if($product->created_at && $product->created_at->isAfter(now()->subDays(30)))
                <span class="product-badge product-badge--new">NEW</span>
            @endif
        </div>

        <!-- Desktop Action Buttons (Hover) -->
        @if(!empty($product->uuid))
        <div class="cute-stationery__actions cute-stationery__actions--desktop">
            <button class="cute-stationery__action wishlist-btn" data-product-uuid="{{ $product->uuid }}" title="Add to Wishlist" aria-label="Add to Wishlist">
                <i class="far fa-heart"></i>
            </button>
            <button class="cute-stationery__action cute-stationery__add-cart add-to-cart" data-product-uuid="{{ $product->uuid }}" title="Add to Cart" aria-label="Add to Cart">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>
        @endif
    </div>
    <div class="cute-stationery__info">
        <h3 class="cute-stationery__name">
            <a href="{{ route('product.detail', $product->slug) }}" class="cute-stationery__name-link">
                {{ $product->name }}
            </a>
        </h3>

        <!-- Rating Display -->
        @php
            $avgRating = $product->average_rating ?? 0;
            $reviewsCount = $product->reviews_count ?? 0;
        @endphp
        <div class="cute-stationery__rating">
            @if($reviewsCount > 0)
                <div class="cute-stationery__stars">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($avgRating))
                            <i class="fas fa-star"></i>
                        @elseif($i - 0.5 <= $avgRating)
                            <i class="fas fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                </div>
                <span class="cute-stationery__rating-text">({{ $reviewsCount }})</span>
            @else
                <div class="cute-stationery__stars" style="visibility: hidden;">
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                </div>
            @endif
        </div>

        <div class="cute-stationery__price">
            @if($product->discount_price)
                <span class="cute-stationery__price-current">${{ number_format($product->discount_price, 2) }}</span>
                <span class="cute-stationery__price-old">${{ number_format($product->total_price, 2) }}</span>
            @else
                <span class="cute-stationery__price-current">${{ number_format($product->total_price, 2) }}</span>
            @endif
        </div>
    </div>

    <!-- Mobile Action Buttons (Always Visible) -->
    @if(!empty($product->uuid))
    <div class="cute-stationery__actions cute-stationery__actions--mobile">
        <button class="cute-stationery__action-mobile wishlist-btn" data-product-uuid="{{ $product->uuid }}" aria-label="Add to Wishlist">
            <i class="far fa-heart"></i>
            <span>Wishlist</span>
        </button>
        <button class="cute-stationery__action-mobile cute-stationery__add-cart-mobile add-to-cart" data-product-uuid="{{ $product->uuid }}" aria-label="Add to Cart">
            <i class="fas fa-shopping-cart"></i>
            <span>Add to Cart</span>
        </button>
    </div>
    @endif
</div>

