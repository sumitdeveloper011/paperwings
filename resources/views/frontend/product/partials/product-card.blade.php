<div class="cute-stationery__item">
    <div class="cute-stationery__image">
        <a href="{{ route('product.detail', $product->slug) }}" class="cute-stationery__image-link">
            <img src="{{ $product->main_thumbnail_url }}" alt="{{ $product->name }}" class="cute-stationery__img" loading="lazy">
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
        <div class="cute-stationery__actions cute-stationery__actions--desktop">
            <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $product->id }}" title="Add to Wishlist" aria-label="Add to Wishlist">
                <i class="far fa-heart"></i>
            </button>
            <button class="cute-stationery__action cute-stationery__add-cart add-to-cart" data-product-id="{{ $product->id }}" title="Add to Cart" aria-label="Add to Cart">
                <i class="fas fa-shopping-cart"></i>
            </button>
        </div>
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
        @if($reviewsCount > 0)
            <div class="cute-stationery__rating">
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
            </div>
        @endif

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
    <div class="cute-stationery__actions cute-stationery__actions--mobile">
        <button class="cute-stationery__action-mobile wishlist-btn" data-product-id="{{ $product->id }}" aria-label="Add to Wishlist">
            <i class="far fa-heart"></i>
            <span>Wishlist</span>
        </button>
        <button class="cute-stationery__action-mobile cute-stationery__add-cart-mobile add-to-cart" data-product-id="{{ $product->id }}" aria-label="Add to Cart">
            <i class="fas fa-shopping-cart"></i>
            <span>Add to Cart</span>
        </button>
    </div>
</div>

