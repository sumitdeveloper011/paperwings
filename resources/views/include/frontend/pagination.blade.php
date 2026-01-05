<!-- Pagination -->
@php
    // Build pagination elements array (similar to Laravel's default structure)
    $elements = [];
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();

    // Show first page if we're not near the start
    if ($currentPage > 3) {
        $elements[] = [1 => $paginator->url(1)];
        if ($currentPage > 4) {
            $elements[] = '...';
        }
    }

    // Show pages around current page
    $start = max(1, $currentPage - 2);
    $end = min($lastPage, $currentPage + 2);

    $pageRange = [];
    for ($i = $start; $i <= $end; $i++) {
        $pageRange[$i] = $paginator->url($i);
    }
    if (!empty($pageRange)) {
        $elements[] = $pageRange;
    }

    // Show last page if we're not near the end
    if ($currentPage < $lastPage - 2) {
        if ($currentPage < $lastPage - 3) {
            $elements[] = '...';
        }
        $elements[] = [$lastPage => $paginator->url($lastPage)];
    }
    
    // Get previous and next URLs with query string preserved
    $previousUrl = $paginator->previousPageUrl();
    $nextUrl = $paginator->nextPageUrl();
@endphp

@if($paginator->hasPages())
<div class="pagination-wrapper">
    <nav aria-label="Products pagination">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link" tabindex="-1" aria-disabled="true">Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">Previous</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if(is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if(is_array($element))
                    @foreach($element as $page => $url)
                        @if($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link" aria-disabled="true">Next</span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif
