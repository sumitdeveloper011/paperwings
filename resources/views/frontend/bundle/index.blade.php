@extends('layouts.frontend.main')

@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Product Bundles',
        'subtitle' => 'Save more with our special product bundles',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Product Bundles', 'url' => null]
        ]
    ])

    <section class="bundles-section">
        <div class="container">
            @if($bundles && $bundles->count() > 0)
            @include('frontend.partials.products-header', [
                'products' => $bundles,
                'sort' => $sort ?? null,
                'sortOptions' => [
                    'featured' => 'Sort by: Featured',
                    'price_low_high' => 'Price: Low to High',
                    'price_high_low' => 'Price: High to Low',
                    'name_asc' => 'Name: A to Z',
                    'name_desc' => 'Name: Z to A',
                    'newest' => 'Newest First',
                ]
            ])

            <div class="products-grid" id="bundlesGrid">
                @foreach($bundles as $bundle)
                    @include('frontend.bundle.partials.bundle-card', ['bundle' => $bundle])
                @endforeach
            </div>

            @if($bundles && $bundles->hasPages())
                @include('include.frontend.pagination', ['paginator' => $bundles])
            @endif
            @else
            <div class="products-grid__empty">
                <div class="empty-state">
                    <i class="fas fa-box-open empty-state__icon"></i>
                    <p class="empty-state__text">No bundles available at the moment. Check back soon!</p>
                </div>
            </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
<style>
.bundles-section {
    padding: 3rem 0 4rem;
}

.cute-stationery__action {
    text-decoration: none;
}

.cute-stationery__action:hover {
    color: var(--coral-red);
}

</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sortSelect');

        // Sort functionality
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const selectedSort = this.value;
                const currentUrl = new URL(window.location.href);

                // Update or add sort parameter
                currentUrl.searchParams.set('sort', selectedSort);

                // Reset to page 1 when sorting changes
                currentUrl.searchParams.delete('page');

                // Redirect to new URL with sort parameter
                window.location.href = currentUrl.toString();
            });
        }

        // Bundle Add to Cart functionality
        document.querySelectorAll('.bundle-add-to-cart').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const bundleId = this.getAttribute('data-bundle-id');
                if (bundleId) {
                    // Redirect to bundle detail page where user can add to cart
                    window.location.href = '/bundles/' + bundleId;
                }
            });
        });
    });
</script>
@endpush

