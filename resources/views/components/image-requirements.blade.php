@props([
    'type' => 'default', // category, product, slider, page, brand, etc.
    'show' => true, // Show or hide the requirements box
])

@if($show)
    @php
        $dimensions = [
            'category' => [
                'original' => '500x500 pixels (square)',
                'thumbnail' => '200x200 pixels (auto-generated)',
                'tip' => 'Use square images (1:1 ratio) for best results. The system will crop images to fit the square format.',
            ],
            'product' => [
                'original' => '1200x1200 pixels (square)',
                'thumbnail' => '300x300 pixels (auto-generated)',
                'tip' => 'Use high-quality square images (1:1 ratio). Images will be automatically resized to 1200x1200 (original), 600x600 (medium), and 300x300 (thumbnail). Multiple images are supported.',
            ],
            'slider' => [
                'original' => '1920x600 pixels',
                'thumbnail' => '320x100 pixels (auto-generated)',
                'tip' => 'Use high-resolution images with 1920x600 dimensions (3.2:1 ratio) for best display on sliders.',
                'hide_format' => true,
            ],
            'page' => [
                'original' => 'Original size maintained',
                'thumbnail' => '400x400 pixels (auto-generated)',
                'tip' => 'Banner images will be displayed at full width.',
            ],
            'brand' => [
                'original' => 'Original size maintained',
                'thumbnail' => '400x400 pixels (auto-generated)',
                'tip' => 'Logo images should be clear and recognizable.',
            ],
            'subcategory' => [
                'original' => '800x800 pixels (square)',
                'thumbnail' => '400x400 pixels (auto-generated)',
                'tip' => 'Use square images (1:1 ratio) for best results.',
            ],
            'default' => [
                'original' => 'Original size maintained',
                'thumbnail' => '400x400 pixels (auto-generated)',
                'tip' => 'Use high-quality images for best results.',
            ],
        ];

        $config = $dimensions[$type] ?? $dimensions['default'];
    @endphp

    <div class="alert-image-requirements">
        <div class="alert-image-requirements__content">
            <i class="fas fa-info-circle alert-image-requirements__icon"></i>
            <div>
                <strong class="alert-image-requirements__title">Image Requirements:</strong>
                <ul class="alert-image-requirements__list">
                    <li><strong>Dimensions:</strong> Images will be automatically resized to <strong>{{ $config['original'] }}</strong></li>
                    @if(!isset($config['hide_format']) || !$config['hide_format'])
                        <li><strong>Format:</strong> JPEG, PNG, JPG, GIF (validated on upload)</li>
                        <li><strong>Max Size:</strong> 2MB (validated on upload)</li>
                    @endif
                    @if(isset($config['tip']))
                        <li><strong>Tip:</strong> {{ $config['tip'] }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif
