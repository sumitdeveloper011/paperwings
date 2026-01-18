@extends('layouts.frontend.main')

@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Galleries',
        'subtitle' => 'Browse our image and video galleries',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Galleries', 'url' => null]
        ]
    ])

    <section class="galleries-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="gallery-filters">
                        <div class="filter-buttons">
                            <a href="{{ route('galleries.index') }}" class="btn {{ !$category ? 'active' : '' }}">
                                <i class="fas fa-th"></i>
                                <span>All Galleries</span>
                            </a>
                            @foreach($categories as $key => $label)
                                <a href="{{ route('galleries.index', ['category' => $key]) }}" 
                                   class="btn {{ $category === $key ? 'active' : '' }}">
                                    @switch($key)
                                        @case('general')
                                            <i class="fas fa-images"></i>
                                            @break
                                        @case('products')
                                            <i class="fas fa-box"></i>
                                            @break
                                        @case('events')
                                            <i class="fas fa-calendar-alt"></i>
                                            @break
                                        @case('portfolio')
                                            <i class="fas fa-briefcase"></i>
                                            @break
                                        @default
                                            <i class="fas fa-folder"></i>
                                    @endswitch
                                    <span>{{ $label }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if($galleries->count() > 0)
                        <div class="galleries-grid">
                            @foreach($galleries as $gallery)
                                <div class="gallery-card">
                                    <a href="{{ route('gallery.show', $gallery->slug) }}" class="gallery-card__link">
                                        @if($gallery->coverImage)
                                            <div class="gallery-card__image">
                                                <img src="{{ asset('storage/' . $gallery->coverImage->image_path) }}" 
                                                     alt="{{ $gallery->name }}"
                                                     loading="lazy"
                                                     onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                            </div>
                                        @elseif($gallery->items->count() > 0)
                                            <div class="gallery-card__image">
                                                @php
                                                    $firstItem = $gallery->items->first();
                                                @endphp
                                                @if($firstItem->type === 'image' && $firstItem->image_path)
                                                    <img src="{{ asset('storage/' . $firstItem->image_path) }}" 
                                                         alt="{{ $gallery->name }}"
                                                         loading="lazy"
                                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                                @elseif($firstItem->type === 'video' && $firstItem->thumbnail_path)
                                                    <img src="{{ asset('storage/' . $firstItem->thumbnail_path) }}" 
                                                         alt="{{ $gallery->name }}"
                                                         loading="lazy"
                                                         onerror="this.src='{{ asset('assets/images/video-placeholder.jpg') }}'">
                                                    <div class="gallery-card__video-badge">
                                                        <i class="fas fa-play"></i>
                                                    </div>
                                                @else
                                                    <div class="gallery-card__placeholder">
                                                        <i class="fas fa-images"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="gallery-card__image">
                                                <div class="gallery-card__placeholder">
                                                    <i class="fas fa-images"></i>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="gallery-card__overlay">
                                            <div class="gallery-card__info">
                                                <h3 class="gallery-card__title">{{ $gallery->name }}</h3>
                                                <p class="gallery-card__count">
                                                    <i class="fas fa-images"></i>
                                                    {{ $gallery->items->count() }} {{ Str::plural('item', $gallery->items->count()) }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        @if($galleries->hasPages())
                            <div class="pagination-wrapper">
                                {{ $galleries->links('components.pagination') }}
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <div class="empty-state__icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3 class="empty-state__title">No Galleries Found</h3>
                            <p class="empty-state__text">
                                @if($category)
                                    No galleries found in this category. Try browsing all galleries or select a different category.
                                @else
                                    No galleries available at the moment. Please check back later.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
