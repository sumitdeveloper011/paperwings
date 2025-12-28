{{-- Wishlist Items Collection Partial --}}
@forelse($items as $item)
    @if($item->product)
        @include('frontend.wishlist.partials.item', ['item' => $item])
    @endif
@empty
    {{-- Empty state handled by JavaScript --}}
@endforelse

