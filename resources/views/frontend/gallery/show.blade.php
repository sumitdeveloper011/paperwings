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
<div class="container py-5">
    <div class="page-header mb-4">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('galleries.index') }}">Galleries</a></li>
                <li class="breadcrumb-item active">{{ $gallery->name }}</li>
            </ol>
        </nav>
        <h1 class="page-title">{{ $gallery->name }}</h1>
        @if($gallery->description)
            <p class="page-subtitle">{{ $gallery->description }}</p>
        @endif
    </div>

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
@endsection
