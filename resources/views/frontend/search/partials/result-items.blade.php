{{-- Search Result Items Collection Partial --}}
@forelse($products as $product)
    @include('frontend.search.partials.result-item', ['product' => $product])
@empty
    <div class="search-result-item" style="text-align: center; color: #6c757d;">No products found</div>
@endforelse

