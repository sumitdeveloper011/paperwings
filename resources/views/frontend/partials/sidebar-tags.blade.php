{{-- Tags Widget Partial --}}
{{-- Usage: @include('frontend.partials.sidebar-tags', ['tags' => $tags, 'selectedTags' => []]) --}}

@if(isset($tags) && $tags->count() > 0)
<div class="sidebar-widget">
    <div class="sidebar-widget__header">
        <h3 class="sidebar-widget__title">Tags</h3>
    </div>
    <div class="sidebar-widget__body">
        <!-- Tag Search Box -->
        @if($tags->count() > 5)
        <div class="category-search-box mb-3">
            <input type="text" class="category-search-input" id="tagSearch" placeholder="Search tags...">
            <i class="fas fa-search category-search-icon"></i>
        </div>
        @endif

        <!-- Tags List Container -->
        <div class="categories-list-container" id="tagsListContainer">
            <ul class="sidebar-categories" id="tagsList">
                @foreach($tags as $tag)
                <li class="sidebar-category"
                    data-category-name="{{ strtolower($tag->name) }}">
                    <label class="sidebar-category__link sidebar-category__link--checkbox">
                        <input class="tag-filter" type="checkbox" value="{{ $tag->id }}" id="tag{{ $tag->id }}"
                            {{ in_array($tag->id, $selectedTags ?? []) ? 'checked' : '' }}>
                        <span class="sidebar-category__name">{{ $tag->name }} <span class="category-count">({{ $tag->products_count ?? 0 }})</span></span>
                    </label>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
