@extends('layouts.frontend.main')

@section('content')
<div class="container py-5">
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="fas fa-images"></i>
            Galleries
        </h1>
        <p class="page-subtitle">Browse our image and video galleries</p>
    </div>

    @if($category || request()->has('category'))
    <div class="gallery-filters mb-4">
        <div class="filter-buttons">
            <a href="{{ route('galleries.index') }}" class="btn btn-outline-primary {{ !$category ? 'active' : '' }}">
                All Galleries
            </a>
            @foreach($categories as $key => $label)
                <a href="{{ route('galleries.index', ['category' => $key]) }}" 
                   class="btn btn-outline-primary {{ $category === $key ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($galleries->count() > 0)
        <div class="galleries-grid">
            @foreach($galleries as $gallery)
                <div class="gallery-card">
                    <a href="{{ route('gallery.show', $gallery->slug) }}" class="gallery-card__link">
                        @if($gallery->coverImage)
                            <div class="gallery-card__image">
                                <img src="{{ asset('storage/' . $gallery->coverImage->image_path) }}" 
                                     alt="{{ $gallery->name }}"
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
                                         onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                @elseif($firstItem->type === 'video' && $firstItem->thumbnail_path)
                                    <img src="{{ asset('storage/' . $firstItem->thumbnail_path) }}" 
                                         alt="{{ $gallery->name }}"
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
            <div class="pagination-wrapper mt-4">
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
                    No galleries found in this category.
                @else
                    No galleries available at the moment.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection
