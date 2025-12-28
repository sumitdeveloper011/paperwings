{{-- Search Autocomplete Item Partial --}}
<a href="{{ route('product.detail', $product->slug) }}" class="search-autocomplete-item">
    @if($product->relationLoaded('images') && $product->images->isNotEmpty())
        <img src="{{ $product->images->first()->image_url }}" alt="{{ $product->name }}">
    @else
        <img src="{{ asset('assets/images/placeholder.jpg') }}" alt="{{ $product->name }}">
    @endif
    <div>
        <div class="search-autocomplete-name">{{ $product->name }}</div>
        <div class="search-autocomplete-price">
            @if($product->discount_price)
                <span style="text-decoration: line-through; color: #6c757d; margin-right: 0.5rem;">
                    ${{ number_format($product->total_price, 2) }}
                </span>
                <span>${{ number_format($product->discount_price, 2) }}</span>
            @else
                <span>${{ number_format($product->total_price, 2) }}</span>
            @endif
        </div>
    </div>
</a>

