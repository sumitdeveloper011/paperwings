@extends('layouts.frontend.main')

@push('head')
<meta name="description" content="{{ $gallery->description ?? 'Gallery: ' . $gallery->name }}">
<meta property="og:title" content="{{ $gallery->name }}">
<meta property="og:description" content="{{ $gallery->description ?? 'Gallery: ' . $gallery->name }}">
@if($gallery->coverImage)
<meta property="og:image" content="{{ asset('storage/' . $gallery->coverImage->image_path) }}">
@endif
<meta property="og:url" content="{{ route('gallery.show', $gallery->slug) }}">
@endpush

@section('content')
    @include('frontend.partials.page-header', [
        'title' => $gallery->name,
        'subtitle' => $gallery->description ?? null,
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Galleries', 'url' => route('galleries.index')],
            ['label' => $gallery->name, 'url' => null]
        ]
    ])

    <section class="gallery-detail-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    @if($gallery->items->count() > 0)
                        @php
                            $hasImages = $gallery->items->where('type', 'image')->count() > 0;
                            $hasVideos = $gallery->items->where('type', 'video')->count() > 0;
                        @endphp

                        @if($hasImages && $hasVideos)
                        <div class="gallery-controls">
                            <button class="gallery-controls__btn active" data-filter="all">
                                <i class="fas fa-th"></i>
                                <span>All Items</span>
                            </button>
                            <button class="gallery-controls__btn" data-filter="image">
                                <i class="fas fa-image"></i>
                                <span>Images ({{ $gallery->items->where('type', 'image')->count() }})</span>
                            </button>
                            <button class="gallery-controls__btn" data-filter="video">
                                <i class="fas fa-video"></i>
                                <span>Videos ({{ $gallery->items->where('type', 'video')->count() }})</span>
                            </button>
                        </div>
                        @endif

                        <x-gallery :gallery="$gallery" />
                    @else
                        <div class="empty-state">
                            <div class="empty-state__icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3 class="empty-state__title">No Items Yet</h3>
                            <p class="empty-state__text">This gallery is empty. Please check back later for new content.</p>
                            <a href="{{ route('galleries.index') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left"></i> Back to Galleries
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.gallery-controls__btn');
    const galleryItems = document.querySelectorAll('.gallery-item');

    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');

                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                galleryItems.forEach(item => {
                    const itemType = item.getAttribute('data-item-type');

                    if (filter === 'all' || filter === itemType) {
                        item.style.display = 'block';
                        setTimeout(() => {
                            item.style.opacity = '1';
                            item.style.transform = 'scale(1)';
                        }, 10);
                    } else {
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            item.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    }
});
</script>
@endpush
