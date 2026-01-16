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
            'bundle' => [
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
                'original' => '1200x400 pixels (Main - 3:1 ratio)',
                'medium' => '600x200 pixels (auto-generated)',
                'thumbnail' => '300x100 pixels (auto-generated)',
                'tip' => 'Use high-quality banner images with 1200x400 dimensions (3:1 ratio). Images will be automatically resized to 1200x400 (main), 600x200 (medium), and 300x100 (thumbnail).',
            ],
            'about-section' => [
                'original' => '600x400 pixels (Main - 3:2 ratio)',
                'thumbnail' => '300x200 pixels (auto-generated)',
                'tip' => 'Use high-quality images with 600x400 dimensions (3:2 ratio). Images will be automatically resized to 600x400 (main) and 300x200 (thumbnail).',
            ],
            'testimonial' => [
                'original' => '200x200 pixels (Main - square)',
                'thumbnail' => '100x100 pixels (auto-generated)',
                'tip' => 'Use high-quality square profile images (1:1 ratio). Images will be automatically resized to 200x200 (main) and 100x100 (thumbnail).',
            ],
            'special-offers' => [
                'original' => '1920x450 pixels (Main - 4.27:1 ratio)',
                'medium' => '960x225 pixels (auto-generated)',
                'thumbnail' => '480x112 pixels (auto-generated)',
                'tip' => 'Use high-quality banner images with 1920x450 dimensions (4.27:1 ratio). Images will be automatically resized to 1920x450 (main), 960x225 (medium), and 480x112 (thumbnail).',
            ],
            'user' => [
                'original' => '200x200 pixels (Main - square)',
                'thumbnail' => '100x100 pixels (auto-generated)',
                'tip' => 'Use high-quality square profile images (1:1 ratio). Images will be automatically resized to 200x200 (main) and 100x100 (thumbnail).',
            ],
            'logo' => [
                'original' => '1640x762 pixels (Main)',
                'medium' => '410x190 pixels (auto-generated)',
                'thumbnail' => '300x140 pixels (auto-generated)',
                'tip' => 'Use high-quality logo images with 1640x762 dimensions. Images will be automatically resized to 1640x762 (main), 410x190 (medium), and 300x140 (thumbnail).',
            ],
            'icon' => [
                'original' => '64x64 pixels (Main - square)',
                'thumbnail' => '32x32 pixels (auto-generated)',
                'tip' => 'Use high-quality square favicon images (1:1 ratio). Images will be automatically resized to 64x64 (main) and 32x32 (thumbnail).',
            ],
            'breadcrumb' => [
                'original' => '1920x400 pixels (Main - 4.8:1 ratio)',
                'medium' => '960x200 pixels (auto-generated)',
                'thumbnail' => '480x100 pixels (auto-generated)',
                'tip' => 'Use high-quality banner images with 1920x400 dimensions (4.8:1 ratio) for breadcrumb backgrounds. Images will be automatically resized to 1920x400 (main), 960x200 (medium), and 480x100 (thumbnail).',
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
            'gallery' => [
                'original' => '2000x2000 pixels (square)',
                'medium' => '1000x1000 pixels (auto-generated)',
                'thumbnail' => '300x300 pixels (auto-generated)',
                'tip' => 'Use high-quality square images (1:1 ratio). Images will be automatically resized to 2000x2000 (main), 1000x1000 (medium), and 300x300 (thumbnail).',
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
                    <li><strong>Main:</strong> <strong>{{ $config['original'] }}</strong></li>
                    @if(isset($config['medium']))
                        <li><strong>Medium:</strong> {{ $config['medium'] }}</li>
                    @endif
                    <li><strong>Thumbnail:</strong> {{ $config['thumbnail'] ?? 'Auto-generated' }}</li>
                    @if(!isset($config['hide_format']) || !$config['hide_format'])
                        @php
                            $formats = ['JPEG', 'PNG', 'JPG', 'GIF'];
                            if (in_array($type, ['product', 'bundle', 'page', 'about-section', 'testimonial', 'special-offers', 'user', 'logo', 'icon', 'gallery'])) {
                                $formats[] = 'WEBP';
                            }
                            $formatString = implode(', ', $formats);
                        @endphp
                        <li><strong>Format:</strong> {{ $formatString }} (validated on upload)</li>
                        <li><strong>Max Size:</strong> {{ $type === 'gallery' ? '5MB' : '2MB' }} (validated on upload)</li>
                    @endif
                    @if(isset($config['tip']))
                        <li><strong>Tip:</strong> {{ $config['tip'] }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif
