{{-- Categories Widget Partial --}}
{{-- Usage: @include('frontend.partials.sidebar-categories', ['categories' => $categories, 'type' => 'link|checkbox', 'selectedCategories' => [], 'currentCategory' => null]) --}}

@php
    $type = $type ?? 'link'; // 'link' for category page, 'checkbox' for shop page
    $selectedCategories = $selectedCategories ?? [];
    $currentCategory = $currentCategory ?? null;
@endphp

<div class="sidebar-widget">
    <div class="sidebar-widget__header">
        <h3 class="sidebar-widget__title">Categories</h3>
    </div>
    <div class="sidebar-widget__body">
        <!-- Category Search Box -->
        @if($categories && $categories->count() > 5)
        <div class="category-search-box mb-3">
            <input type="text" class="category-search-input" id="categorySearch" placeholder="Search categories...">
            <i class="fas fa-search category-search-icon"></i>
        </div>
        @endif

        <!-- Categories List Container -->
        <div class="categories-list-container" id="categoriesListContainer">
            <ul class="sidebar-categories" id="categoriesList">
                @if($categories && $categories->count() > 0)
                @foreach($categories as $catItem)
                <li class="sidebar-category"
                    data-category-name="{{ strtolower($catItem->name) }}">
                    @if($type === 'link')
                        <a href="{{ route('category.show', $catItem->slug) }}"
                           class="sidebar-category__link {{ $currentCategory && $currentCategory->slug == $catItem->slug ? 'active' : '' }}">
                            <span class="sidebar-category__name">{{ $catItem->name }}</span>
                            <span class="category-count">({{ $catItem->active_products_count ?? 0 }})</span>
                        </a>
                    @else
                        <label class="sidebar-category__link sidebar-category__link--checkbox">
                            <input class="category-filter" type="checkbox" value="{{ $catItem->id }}" id="category{{ $catItem->id }}"
                                {{ in_array($catItem->id, $selectedCategories) ? 'checked' : '' }}>
                            <span class="sidebar-category__name">{{ $catItem->name }} <span class="category-count">({{ $catItem->active_products_count ?? 0 }})</span></span>
                        </label>
                    @endif
                </li>
                @endforeach
                @else
                <li class="sidebar-category--empty">
                    <span class="sidebar-category__empty-text">No categories available</span>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
