{{-- Wishlist Item Partial --}}
@if(!empty($item->product->uuid))
<div class="wishlist-sidebar-item" data-product-uuid="{{ $item->product->uuid }}">
    <div class="wishlist-sidebar-item__checkbox">
        <label class="checkbox-label">
            <input type="checkbox" class="wishlist-item-checkbox" data-product-uuid="{{ $item->product->uuid }}">
            <span class="checkmark"></span>
        </label>
    </div>
    <div class="wishlist-sidebar-item__image">
        <a href="{{ route('product.detail', $item->product->slug) }}">
            @if($item->product->relationLoaded('images') && $item->product->images->isNotEmpty())
                <img src="{{ $item->product->images->first()->thumbnail_url }}" 
                     alt="{{ $item->product->name }}">
            @else
                <img src="{{ asset('assets/images/placeholder.jpg') }}" 
                     alt="{{ $item->product->name }}">
            @endif
        </a>
    </div>
    <div class="wishlist-sidebar-item__info">
        <h4 class="wishlist-sidebar-item__name">
            <a href="{{ route('product.detail', $item->product->slug) }}">
                {{ $item->product->name }}
            </a>
        </h4>
        <div class="wishlist-sidebar-item__price-row">
            @if($item->product->discount_price)
                <span class="wishlist-sidebar-item__price" style="text-decoration: line-through; color: #6c757d; font-size: 0.85rem; margin-right: 0.5rem;">
                    ${{ number_format($item->product->total_price, 2) }}
                </span>
            @endif
            <span class="wishlist-sidebar-item__price">
                ${{ number_format($item->product->discount_price ?? $item->product->total_price, 2) }}
            </span>
        </div>
        @if(!empty($item->product->uuid))
        <button class="wishlist-sidebar-item__add-cart btn btn-primary btn-sm" 
                data-product-uuid="{{ $item->product->uuid }}" 
                title="Add to Cart" 
                style="margin-top: 0.5rem; width: 100%;">
            <i class="fas fa-shopping-cart"></i> <span>Add to Cart</span>
        </button>
        @endif
    </div>
    @if(!empty($item->product->uuid))
    <button class="wishlist-sidebar-item__remove" 
            data-product-uuid="{{ $item->product->uuid }}" 
            title="Remove">
        <i class="fas fa-times"></i>
    </button>
    @endif
</div>
@endif

