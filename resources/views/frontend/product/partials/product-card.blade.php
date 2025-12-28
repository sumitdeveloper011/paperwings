<div class="cute-stationery__item">
    <div class="cute-stationery__image">
        <a href="{{ route('product.detail', $product->slug) }}" class="cute-stationery__image-link">
            <img src="{{ $product->main_image }}" alt="{{ $product->name }}" class="cute-stationery__img" loading="lazy">
        </a>
        
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
        
        <div class="cute-stationery__actions">
            <button class="cute-stationery__action wishlist-btn" data-product-id="{{ $product->id }}" title="Add to Wishlist"><i class="fas fa-heart"></i></button>
            <button class="cute-stationery__action cute-stationery__add-cart add-to-cart" data-product-id="{{ $product->id }}" title="Add to Cart"><i class="fas fa-shopping-cart"></i></button>
        </div>
    </div>
    <div class="cute-stationery__info">
        <h3 class="cute-stationery__name">
            <a href="{{ route('product.detail', $product->slug) }}" class="cute-stationery__name-link">
                {{ $product->name }}
            </a>
        </h3>
        
        <div class="cute-stationery__price">
            @if($product->discount_price)
                <span class="cute-stationery__price-current">${{ number_format($product->discount_price, 2) }}</span>
                <span class="cute-stationery__price-old">${{ number_format($product->total_price, 2) }}</span>
            @else
                <span class="cute-stationery__price-current">${{ number_format($product->total_price, 2) }}</span>
            @endif
        </div>
    </div>
</div>

