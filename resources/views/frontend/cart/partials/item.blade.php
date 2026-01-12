{{-- Cart Sidebar Item Partial --}}
<div class="cart-sidebar-item" data-cart-item-id="{{ $item->id }}">
    <div class="cart-sidebar-item__image">
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
    <div class="cart-sidebar-item__info">
        <h4 class="cart-sidebar-item__name">
            <a href="{{ route('product.detail', $item->product->slug) }}" class="cart-sidebar-item__name-link">
                {{ $item->product->name }}
            </a>
        </h4>
        <div class="cart-sidebar-item__price-row">
            <span class="cart-sidebar-item__price">
                ${{ number_format($item->price, 2) }} x {{ $item->quantity }}
            </span>
            <span class="cart-sidebar-item__total">
                = ${{ number_format($item->subtotal, 2) }}
            </span>
        </div>
    </div>
    <button class="cart-sidebar-item__remove"
            data-cart-item-id="{{ $item->id }}"
            title="Remove">
        <i class="fas fa-times"></i>
    </button>
</div>

