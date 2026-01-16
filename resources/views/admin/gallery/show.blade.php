@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-images"></i>
                    {{ $gallery->name }}
                </h1>
                <p class="page-header__subtitle">Manage gallery items</p>
            </div>
            <div class="page-header__actions">
                @can('galleries.edit')
                <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit Gallery</span>
                </a>
                @endcan
                <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Galleries</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Gallery Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-heading"></i>
                                Name
                            </div>
                            <div class="detail-item__value">
                                <strong>{{ $gallery->name }}</strong>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-folder"></i>
                                Category
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge-primary">{{ ucfirst($gallery->category) }}</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="detail-item__value">
                                <span class="badge badge-{{ $gallery->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($gallery->status) }}
                                </span>
                            </div>
                        </div>

                        @if($gallery->description)
                        <div class="detail-item detail-item--full">
                            <div class="detail-item__label">
                                <i class="fas fa-align-left"></i>
                                Description
                            </div>
                            <div class="detail-item__value">
                                <div class="description-content">
                                    {{ $gallery->description }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modern-card gallery-card-spacing">
                <div class="modern-card__header">
                    <div class="modern-card__header-content">
                        <h3 class="modern-card__title">
                            <i class="fas fa-images"></i>
                            Gallery Items ({{ $gallery->items->count() }})
                        </h3>
                    </div>
                    <div class="modern-card__header-actions">
                        @can('gallery-items.upload')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addImageModal">
                            <i class="fas fa-plus"></i>
                            Add Image
                        </button>
                        <button type="button" class="btn btn-info btn-sm gallery-btn-spacing" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                            <i class="fas fa-video"></i>
                            Add Video
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="modern-card__body">
                    @include('admin.gallery.partials.item-grid', ['gallery' => $gallery, 'items' => $gallery->items])
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Gallery Details
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-link"></i>
                                Slug
                            </div>
                            <div class="detail-item__value">
                                <code class="code-block">{{ $gallery->slug }}</code>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-user"></i>
                                Created By
                            </div>
                            <div class="detail-item__value">
                                {{ $gallery->creator->name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-calendar-alt"></i>
                                Created At
                            </div>
                            <div class="detail-item__value">
                                {{ $gallery->created_at->format('M d, Y h:i A') }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-item__label">
                                <i class="fas fa-calendar-check"></i>
                                Updated At
                            </div>
                            <div class="detail-item__value">
                                {{ $gallery->updated_at->format('M d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@can('gallery-items.upload')
@include('admin.gallery.partials.upload-modal', ['gallery' => $gallery])
@include('admin.gallery.partials.video-embed-form', ['gallery' => $gallery])
@endcan

@push('scripts')
<script src="{{ asset('assets/js/admin-gallery.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    AdminGallery.initDragDrop('gallery-items-grid', '{{ route('admin.gallery-items.reorder', $gallery) }}');
});
</script>
@endpush
@endsection
