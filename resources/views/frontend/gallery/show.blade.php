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
                        <x-gallery :gallery="$gallery" />
                    @else
                        <div class="empty-state">
                            <div class="empty-state__icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3 class="empty-state__title">No Items Yet</h3>
                            <p class="empty-state__text">This gallery is empty.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
