{{--
    ============================================
    Reusable Page Header Component (DRY Approach)
    ============================================

    Usage Examples:

    1. Simple usage (just title):
       @include('frontend.partials.page-header', ['title' => 'Page Title'])

    2. With subtitle:
       @include('frontend.partials.page-header', [
           'title' => 'Page Title',
           'subtitle' => 'Optional subtitle text'
       ])

    3. With custom breadcrumbs:
       @include('frontend.partials.page-header', [
           'title' => 'Page Title',
           'subtitle' => 'Optional subtitle',
           'breadcrumbs' => [
               ['label' => 'Home', 'url' => route('home')],
               ['label' => 'Category', 'url' => route('category.show', 'slug')],
               ['label' => 'Current Page', 'url' => null] // null = active item
           ]
       ])

    4. With background image (optional):
       @include('frontend.partials.page-header', [
           'title' => 'Page Title',
           'subtitle' => 'Optional subtitle',
           'image' => asset('assets/frontend/images/header-bg.jpg') // Optional
       ])
--}}

@php
    // Default breadcrumbs if not provided
    if (!isset($breadcrumbs)) {
        $breadcrumbs = [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => $title ?? 'Page', 'url' => null]
        ];
    }

    // Ensure breadcrumbs is an array
    if (!is_array($breadcrumbs)) {
        $breadcrumbs = [];
    }

    // Ensure title exists
    $title = $title ?? 'Page Title';

    // Check if image is provided, otherwise use default breadcrumbs.jpg
    if (!isset($image) || empty($image)) {
        // Check in images folder first (where user placed it)
        $defaultImagePath = 'assets/frontend/images/breadcrumbs.jpg';
        if (file_exists(public_path($defaultImagePath))) {
            $image = asset($defaultImagePath);
        } else {
            // Fallback to headers folder
            $defaultImagePath = 'assets/frontend/images/headers/breadcrumbs.jpg';
            if (file_exists(public_path($defaultImagePath))) {
                $image = asset($defaultImagePath);
            } else {
                $image = null;
            }
        }
    }

    $hasImage = !empty($image);
    $headerClass = $hasImage ? 'page-header page-header--with-image' : 'page-header';
    // Override entire background property when image is present
    $headerStyle = $hasImage ? "background: url('{$image}') center center / cover no-repeat !important;" : '';
@endphp

<section class="{{ $headerClass }}" style="{{ $headerStyle }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                {{-- Breadcrumb Navigation --}}
                @if(count($breadcrumbs) > 0)
                <nav aria-label="breadcrumb" class="page-header__breadcrumb">
                    <ol class="breadcrumb">
                        @foreach($breadcrumbs as $index => $crumb)
                            @if($index === count($breadcrumbs) - 1 || $crumb['url'] === null)
                                {{-- Last item or active item --}}
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $crumb['label'] }}
                                </li>
                            @else
                                {{-- Link item --}}
                                <li class="breadcrumb-item">
                                    <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
                @endif

                {{-- Page Title --}}
                <h1 class="page-header__title">{{ $title }}</h1>

                {{-- Optional Subtitle --}}
                @if(isset($subtitle) && !empty($subtitle))
                    <p class="page-header__subtitle">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </div>
</section>
