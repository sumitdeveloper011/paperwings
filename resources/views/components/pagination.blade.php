@if ($paginator->hasPages())
    <nav class="pagination-modern" role="navigation" aria-label="Pagination Navigation">
        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item pagination-item--disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="pagination-link pagination-link--disabled">
                        <i class="fas fa-chevron-left"></i>
                        <span class="pagination-link-text">Previous</span>
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <a href="{{ $paginator->previousPageUrl() }}" 
                       class="pagination-link pagination-link--prev" 
                       rel="prev" 
                       aria-label="@lang('pagination.previous')">
                        <i class="fas fa-chevron-left"></i>
                        <span class="pagination-link-text">Previous</span>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="pagination-item pagination-item--disabled" aria-disabled="true">
                        <span class="pagination-link pagination-link--ellipsis">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item pagination-item--active" aria-current="page">
                                <span class="pagination-link pagination-link--active">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a href="{{ $url }}" class="pagination-link" aria-label="Go to page {{ $page }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a href="{{ $paginator->nextPageUrl() }}" 
                       class="pagination-link pagination-link--next" 
                       rel="next" 
                       aria-label="@lang('pagination.next')">
                        <span class="pagination-link-text">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pagination-item pagination-item--disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="pagination-link pagination-link--disabled">
                        <span class="pagination-link-text">Next</span>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>

        {{-- Pagination Info --}}
        <div class="pagination-info">
            <span class="pagination-info-text">
                Showing 
                <strong>{{ $paginator->firstItem() }}</strong> 
                to 
                <strong>{{ $paginator->lastItem() }}</strong> 
                of 
                <strong>{{ $paginator->total() }}</strong> 
                results
            </span>
        </div>
    </nav>
@endif

