@if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <nav class="pagination-modern" role="navigation" aria-label="Pagination Navigation">
        @if ($paginator->hasPages())
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
            @php
                $paginationElements = $elements ?? null;
                if (!$paginationElements) {
                    if (method_exists($paginator, 'elements')) {
                        try {
                            $paginationElements = $paginator->elements();
                        } catch (\Exception $e) {
                            $paginationElements = null;
                        }
                    }
                    
                    // Fallback: create pagination elements manually with window
                    if (!$paginationElements && $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator) {
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        $paginationElements = [];
                        $onEachSide = 2; // Show 2 pages on each side of current page
                        
                        // Show first page if we're not near the start
                        if ($currentPage > $onEachSide + 1) {
                            $paginationElements[] = [1 => $paginator->url(1)];
                            if ($currentPage > $onEachSide + 2) {
                                $paginationElements[] = '...';
                            }
                        }
                        
                        // Show pages around current page
                        $start = max(1, $currentPage - $onEachSide);
                        $end = min($lastPage, $currentPage + $onEachSide);
                        
                        $pageRange = [];
                        for ($i = $start; $i <= $end; $i++) {
                            $pageRange[$i] = $paginator->url($i);
                        }
                        if (!empty($pageRange)) {
                            $paginationElements[] = $pageRange;
                        }
                        
                        // Show last page if we're not near the end
                        if ($currentPage < $lastPage - $onEachSide) {
                            if ($currentPage < $lastPage - $onEachSide - 1) {
                                $paginationElements[] = '...';
                            }
                            $paginationElements[] = [$lastPage => $paginator->url($lastPage)];
                        }
                    }
                }
            @endphp
            @if($paginationElements && is_array($paginationElements))
            @foreach ($paginationElements as $element)
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
            @endif

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
        @endif

        {{-- Pagination Info - Always show even if no pages --}}
        <div class="pagination-info">
            <span class="pagination-info-text">
                Showing 
                <strong>{{ $paginator->firstItem() ?? 0 }}</strong> 
                to 
                <strong>{{ $paginator->lastItem() ?? 0 }}</strong> 
                of 
                <strong>{{ $paginator->total() }}</strong> 
                results
            </span>
        </div>
    </nav>
@endif

