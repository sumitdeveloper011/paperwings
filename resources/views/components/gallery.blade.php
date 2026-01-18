@props(['gallery'])

<div class="gallery-component" data-gallery-id="{{ $gallery->id }}">
    <div class="gallery-grid" id="gallery-grid">
        @php
            $imageIndex = 0;
        @endphp
        @foreach($gallery->items as $index => $item)
            <div class="gallery-item" data-item-type="{{ $item->type }}" data-item-index="{{ $index }}" style="opacity: 1; transform: scale(1); transition: all 0.3s ease;">
                @if($item->type === 'image')
                    @php
                        $currentImageIndex = $imageIndex;
                        $imageIndex++;
                    @endphp
                    <div class="gallery-item__image-wrapper" data-image-index="{{ $currentImageIndex }}">
                        @if($item->image_path)
                            <x-image-placeholder 
                                src="{{ $item->image_path }}" 
                                alt="{{ $item->alt_text ?? $item->title ?? 'Gallery Image' }}"
                                class="gallery-item__image"
                                :loading="'lazy'" />
                        @endif
                        <div class="gallery-item__overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                        @if($item->title || $item->description)
                            <div class="gallery-item__caption">
                                @if($item->title)
                                    <h4>{{ $item->title }}</h4>
                                @endif
                                @if($item->description)
                                    <p>{{ Str::limit($item->description, 100) }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="gallery-item__video-wrapper">
                        @if($item->thumbnail_path)
                            <div class="gallery-item__video-thumbnail" data-video-wrapper="{{ $item->id }}">
                                <x-image-placeholder 
                                    src="{{ $item->thumbnail_path }}" 
                                    alt="{{ $item->title ?? 'Video' }}"
                                    class="gallery-item__thumbnail-img" />
                                <div class="gallery-item__play-button">
                                    <i class="fas fa-play"></i>
                                </div>
                                
                                @if($item->video_embed_code)
                                    <div class="gallery-item__embed" data-video-id="{{ $item->id }}">
                                        {!! $item->video_embed_code !!}
                                    </div>
                                @elseif($item->video_url)
                                    <div class="gallery-item__video" data-video-id="{{ $item->id }}">
                                        <video controls preload="metadata" class="gallery-item__video-player">
                                            @if(Str::endsWith($item->video_url, '.mp4'))
                                                <source src="{{ $item->video_url }}" type="video/mp4">
                                            @elseif(Str::endsWith($item->video_url, '.webm'))
                                                <source src="{{ $item->video_url }}" type="video/webm">
                                            @elseif(Str::endsWith($item->video_url, '.ogg'))
                                                <source src="{{ $item->video_url }}" type="video/ogg">
                                            @else
                                                <source src="{{ $item->video_url }}" type="video/mp4">
                                            @endif
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                @endif
                            </div>
                        @else
                            @if($item->video_embed_code)
                                <div class="gallery-item__embed gallery-video-active" data-video-id="{{ $item->id }}">
                                    {!! $item->video_embed_code !!}
                                </div>
                            @elseif($item->video_url)
                                <div class="gallery-item__video gallery-video-active" data-video-id="{{ $item->id }}">
                                    <video controls preload="metadata" class="gallery-item__video-player">
                                        @if(Str::endsWith($item->video_url, '.mp4'))
                                            <source src="{{ $item->video_url }}" type="video/mp4">
                                        @elseif(Str::endsWith($item->video_url, '.webm'))
                                            <source src="{{ $item->video_url }}" type="video/webm">
                                        @elseif(Str::endsWith($item->video_url, '.ogg'))
                                            <source src="{{ $item->video_url }}" type="video/ogg">
                                        @else
                                            <source src="{{ $item->video_url }}" type="video/mp4">
                                        @endif
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @endif
                        @endif
                        @if($item->title || $item->description)
                            <div class="gallery-item__caption">
                                @if($item->title)
                                    <h4>{{ $item->title }}</h4>
                                @endif
                                @if($item->description)
                                    <p>{{ Str::limit($item->description, 100) }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if($gallery->items->where('type', 'image')->count() > 0)
    <div class="lightbox" id="lightbox">
        <button class="lightbox__close" aria-label="Close lightbox">
            <i class="fas fa-times"></i>
        </button>
        <button class="lightbox__prev" aria-label="Previous image">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="lightbox__next" aria-label="Next image">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="lightbox__content">
            <img src="" alt="" id="lightbox-image">
            <div class="lightbox__caption" id="lightbox-caption"></div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="{{ asset('assets/frontend/js/gallery-lightbox.js') }}"></script>
<script src="{{ asset('assets/frontend/js/gallery-video.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @php
        $imageItems = $gallery->items->where('type', 'image')->map(function($item) {
            return [
                'image' => asset('storage/' . $item->image_path),
                'title' => $item->title ?? null,
                'description' => $item->description ?? null
            ];
        })->values()->all();
    @endphp
    const galleryItems = @json($imageItems);

    if (galleryItems.length > 0 && typeof GalleryLightbox !== 'undefined') {
        GalleryLightbox.init(galleryItems);

        document.querySelectorAll('.gallery-item__image-wrapper').forEach((wrapper) => {
            const index = parseInt(wrapper.dataset.imageIndex);
            if (!isNaN(index)) {
                wrapper.addEventListener('click', function() {
                    GalleryLightbox.open(index);
                });
            }
        });
    }
});
</script>
@endpush
