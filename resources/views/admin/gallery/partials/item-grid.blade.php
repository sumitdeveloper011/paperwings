@if($items && $items->count() > 0)
    <div id="gallery-items-grid" class="gallery-items-grid">
        @foreach($items as $item)
            <div class="gallery-item-card" 
                 data-item-id="{{ $item->id }}" 
                 data-item-uuid="{{ $item->uuid }}"
                 draggable="true">
                <div class="gallery-item-card__header">
                    <div class="gallery-item-card__drag" draggable="false">
                        <i class="fas fa-grip-vertical drag-handle" title="Drag to reorder" draggable="false"></i>
                    </div>
                    <div class="gallery-item-card__actions">
                        @can('gallery-items.delete')
                        <form method="POST" 
                              action="{{ route('admin.gallery-items.destroy', [$gallery, $item]) }}"
                              class="d-inline delete-item-form"
                              onsubmit="return confirm('Are you sure you want to delete this item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                <div class="gallery-item-card__body">
                    @if($item->type === 'image')
                        @if($item->image_path)
                            <div class="gallery-item-card__image">
                                @php
                                    $thumbnailPath = str_replace('/original/', '/thumbnails/', $item->image_path);
                                    $thumbnailUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($thumbnailPath) 
                                        ? asset('storage/' . $thumbnailPath) 
                                        : asset('storage/' . $item->image_path);
                                @endphp
                                <img src="{{ $thumbnailUrl }}" 
                                     alt="{{ $item->alt_text ?? $item->title ?? 'Gallery Image' }}"
                                     class="gallery-item-card__thumbnail"
                                     onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                @if($item->is_featured)
                                    <div class="gallery-item-card__featured-badge">
                                        <i class="fas fa-star"></i>
                                    </div>
                                @else
                                    @can('gallery-items.edit')
                                    <button type="button" 
                                            class="gallery-item-card__featured-btn set-featured-btn" 
                                            data-item-uuid="{{ $item->uuid }}"
                                            title="Set as Featured">
                                        <i class="far fa-star"></i>
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="gallery-item-card__video">
                            @if($item->thumbnail_path)
                                @php
                                    $thumbnailUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists($item->thumbnail_path) 
                                        ? asset('storage/' . $item->thumbnail_path) 
                                        : null;
                                @endphp
                                @if($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" 
                                         alt="{{ $item->title ?? 'Video Thumbnail' }}"
                                         class="gallery-item-card__thumbnail"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'gallery-item-card__video-placeholder\'><i class=\'fas fa-video\'></i></div>';">
                                @else
                                    <div class="gallery-item-card__video-placeholder">
                                        <i class="fas fa-video"></i>
                                    </div>
                                @endif
                            @else
                                <div class="gallery-item-card__video-placeholder">
                                    <i class="fas fa-video"></i>
                                </div>
                            @endif
                            <div class="gallery-item-card__video-badge">
                                <i class="fas fa-play"></i>
                            </div>
                            @if($item->is_featured)
                                <div class="gallery-item-card__featured-badge">
                                    <i class="fas fa-star"></i>
                                </div>
                            @else
                                @can('gallery-items.edit')
                                <button type="button" 
                                        class="gallery-item-card__featured-btn set-featured-btn" 
                                        data-item-uuid="{{ $item->uuid }}"
                                        title="Set as Featured">
                                    <i class="far fa-star"></i>
                                </button>
                                @endcan
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state empty-state--enhanced">
        <div class="empty-state__icon">
            <i class="fas fa-images"></i>
        </div>
        <h3 class="empty-state__title">No Items Yet</h3>
        <p class="empty-state__text">Start by adding images or videos to this gallery</p>
        @can('gallery-items.upload')
        <button type="button" class="btn btn-primary btn-ripple" data-bs-toggle="modal" data-bs-target="#addImageModal">
            <i class="fas fa-plus"></i>
            Add Image
        </button>
        <button type="button" class="btn btn-info btn-ripple gallery-btn-spacing" data-bs-toggle="modal" data-bs-target="#addVideoModal">
            <i class="fas fa-video"></i>
            Add Video
        </button>
        @endcan
    </div>
@endif

@push('scripts')
<script src="{{ asset('assets/js/admin-gallery.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    AdminGallery.initSetFeatured('{{ route('admin.gallery-items.setFeatured', [$gallery, 'ITEM_UUID']) }}');
});
</script>
@endpush
