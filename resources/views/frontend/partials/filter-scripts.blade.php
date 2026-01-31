{{-- Filter JavaScript Partial --}}
{{-- Usage: @include('frontend.partials.filter-scripts', ['type' => 'category|shop', 'priceMax' => $priceMax, 'routeName' => 'shop']) --}}

@php
    $type = $type ?? 'category'; // 'category' or 'shop'
    $routeName = $routeName ?? 'shop';
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sortSelect');
        const priceRange = document.getElementById('priceRange');
        const priceMinDisplay = document.getElementById('priceMinDisplay');
        const priceMaxDisplay = document.getElementById('priceMaxDisplay');
        const applyPriceFilter = document.getElementById('applyPriceFilter');
        const clearPriceFilter = document.getElementById('clearPriceFilter');

        // Sort functionality
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                @if($type === 'shop')
                    updateUrl();
                @else
                    const selectedSort = this.value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('sort', selectedSort);
                    currentUrl.searchParams.delete('page');
                    window.location.href = currentUrl.toString();
                @endif
            });
        }

        // Price filter functionality
        if (priceRange && priceMaxDisplay) {
            const priceMin = parseInt(priceRange.getAttribute('min')) || 0;
            const priceMax = parseInt(priceRange.getAttribute('max')) || 100;

            // Update max price display as slider moves
            priceRange.addEventListener('input', function() {
                const maxValue = this.value;
                priceMaxDisplay.textContent = maxValue;
            });

            // Apply price filter (for category page)
            @if($type === 'category')
            if (applyPriceFilter) {
                applyPriceFilter.addEventListener('click', function() {
                    const currentUrl = new URL(window.location.href);
                    const maxPrice = priceRange.value;

                    if (maxPrice < priceMax) {
                        currentUrl.searchParams.set('min_price', priceMin);
                        currentUrl.searchParams.set('max_price', maxPrice);
                    } else {
                        currentUrl.searchParams.delete('min_price');
                        currentUrl.searchParams.delete('max_price');
                    }

                    currentUrl.searchParams.delete('page');
                    
                    const categoryDrawer = document.getElementById('categoryFilterDrawer');
                    if (categoryDrawer && window.FilterDrawer) {
                        window.FilterDrawer.closeDrawer(categoryDrawer);
                    }
                    
                    window.location.href = currentUrl.toString();
                });
            }
            @endif

            // Clear price filter
            if (clearPriceFilter) {
                clearPriceFilter.addEventListener('click', function(e) {
                    e.preventDefault();
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('min_price');
                    currentUrl.searchParams.delete('max_price');
                    currentUrl.searchParams.delete('page');
                    window.location.href = currentUrl.toString();
                });
            }
        }

        @if($type === 'shop')
        // Shop page specific: Apply Filters and Clear Filters buttons
        const applyFiltersBtn = document.getElementById('applyFilters');
        const clearFiltersBtn = document.getElementById('clearFilters');

        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', function() {
                updateUrl();
            });
        }

        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                document.querySelectorAll('.category-filter, .brand-filter, .tag-filter').forEach(cb => cb.checked = false);
                if (priceRange) {
                    const priceMax = parseInt(priceRange.getAttribute('max')) || 100;
                    priceRange.value = priceMax;
                    if (priceMaxDisplay) {
                        priceMaxDisplay.textContent = priceMax;
                    }
                }
                window.location.href = '{{ route($routeName) }}';
            });
        }

        function updateUrl() {
            const currentUrl = new URL(window.location.href);

            // Get selected categories
            const selectedCategories = Array.from(document.querySelectorAll('.category-filter:checked')).map(cb => cb.value);
            if (selectedCategories.length > 0) {
                currentUrl.searchParams.delete('category');
                currentUrl.searchParams.delete('categories');
                selectedCategories.forEach(id => {
                    currentUrl.searchParams.append('categories[]', id);
                });
            } else {
                currentUrl.searchParams.delete('categories');
            }

            // Get selected brands
            const selectedBrands = Array.from(document.querySelectorAll('.brand-filter:checked')).map(cb => cb.value);
            if (selectedBrands.length > 0) {
                currentUrl.searchParams.delete('brands');
                selectedBrands.forEach(id => {
                    currentUrl.searchParams.append('brands[]', id);
                });
            } else {
                currentUrl.searchParams.delete('brands');
            }

            // Get selected tags
            const selectedTags = Array.from(document.querySelectorAll('.tag-filter:checked')).map(cb => cb.value);
            if (selectedTags.length > 0) {
                currentUrl.searchParams.delete('tags');
                selectedTags.forEach(id => {
                    currentUrl.searchParams.append('tags[]', id);
                });
            } else {
                currentUrl.searchParams.delete('tags');
            }

            // Get price range from slider if applied
            if (priceRange) {
                const maxPrice = priceRange.value;
                const priceMax = parseInt(priceRange.getAttribute('max')) || 100;
                if (maxPrice < priceMax) {
                    currentUrl.searchParams.set('min_price', priceRange.getAttribute('min') || 0);
                    currentUrl.searchParams.set('max_price', maxPrice);
                } else {
                    currentUrl.searchParams.delete('min_price');
                    currentUrl.searchParams.delete('max_price');
                }
            }

            // Get sort
            if (sortSelect && sortSelect.value) {
                currentUrl.searchParams.set('sort', sortSelect.value);
            }

            // Reset to page 1
            currentUrl.searchParams.delete('page');

            // Redirect
            window.location.href = currentUrl.toString();
        }
        @endif
    });
</script>
