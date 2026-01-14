@props([
    'inputId' => 'image',
    'previewId' => 'imagePreview',
    'previewImgId' => 'previewImg',
    'label' => 'Image Preview',
    'show' => true,
])

@if($show)
    <div class="form-group-modern" id="{{ $previewId }}" style="display: none;">
        <label class="form-label-modern">{{ $label }}</label>
        <div class="image-preview">
            <img id="{{ $previewImgId }}" src="" alt="Preview" class="image-preview__img">
            <button type="button" class="image-preview__remove" onclick="removeImagePreview()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
@endif
