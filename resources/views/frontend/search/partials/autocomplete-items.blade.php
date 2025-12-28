{{-- Search Autocomplete Items Collection Partial --}}
@forelse($products as $product)
    @include('frontend.search.partials.autocomplete-item', ['product' => $product])
@empty
    {{-- Empty state handled by JavaScript --}}
@endforelse

