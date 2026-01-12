{{-- Search Result Item Partial --}}
<a href="{{ route('product.detail', $product->slug) }}" class="search-result-item">
    @if($product->relationLoaded('images') && $product->images->isNotEmpty())
        <img src="{{ $product->images->first()->thumbnail_url }}" 
             alt="{{ $product->name }}" 
             class="search-result-item__image" 
             onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
    @else
        <img src="{{ asset('assets/images/placeholder.jpg') }}" 
             alt="{{ $product->name }}" 
             class="search-result-item__image">
    @endif
    <div class="search-result-item__content">
        <div class="search-result-item__name">{{ $product->name }}</div>
        @if($product->category)
            <div class="search-result-item__category">{{ $product->category->name }}</div>
        @endif
        <div class="search-result-item__price">
            @if($product->discount_price)
                <span class="search-result-item__price--old">${{ number_format($product->total_price, 2) }}</span>
                <span>${{ number_format($product->discount_price, 2) }}</span>
            @else
                <span>${{ number_format($product->total_price, 2) }}</span>
            @endif
        </div>
    </div>
</a>

