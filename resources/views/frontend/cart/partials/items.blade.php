{{-- Cart Items Collection Partial --}}
@forelse($items as $item)
    @if($item->product)
        @include('frontend.cart.partials.item', ['item' => $item])
    @endif
@empty
    {{-- Empty state handled by JavaScript --}}
@endforelse

