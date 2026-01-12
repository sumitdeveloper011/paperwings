{{-- Products Grid Partial --}}
{{-- Usage: @include('frontend.partials.products-grid', ['products' => $products, 'emptyMessage' => 'No products found.']) --}}

@php
    $emptyMessage = $emptyMessage ?? 'No products found.';
@endphp

<div class="products-grid" id="productsGrid">
    @if($products && $products->count() > 0)
        @foreach($products as $product)
            @include('frontend.product.partials.product-card', ['product' => $product])
        @endforeach
    @else
    <div class="products-grid__empty">
        <div class="empty-state">
            <i class="fas fa-shopping-bag empty-state__icon"></i>
            <p class="empty-state__text">{{ $emptyMessage }}</p>
        </div>
    </div>
    @endif
</div>

@if($products && $products->hasPages())
    @include('include.frontend.pagination', ['paginator' => $products])
@endif
