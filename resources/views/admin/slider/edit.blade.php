@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Slider
                </h1>
                <p class="page-header__subtitle">Update slider: {{ $slider->heading }}</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.sliders.show', $slider) }}" class="btn btn-outline-info btn-icon">
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                </a>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Sliders</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Slider Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.sliders.update', $slider) }}" enctype="multipart/form-data" class="modern-form" id="sliderForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group-modern">
                            <label for="heading" class="form-label-modern">
                                Heading <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('heading') is-invalid @enderror"
                                       id="heading"
                                       name="heading"
                                       value="{{ old('heading', $slider->heading) }}"
                                       placeholder="Enter slider heading"
                                       required>
                            </div>
                            @error('heading')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="sub_heading" class="form-label-modern">
                                Sub Heading
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-text-height input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('sub_heading') is-invalid @enderror"
                                       id="sub_heading"
                                       name="sub_heading"
                                       value="{{ old('sub_heading', $slider->sub_heading) }}"
                                       placeholder="Enter sub heading (optional)">
                            </div>
                            @error('sub_heading')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="status" class="form-label-modern">
                                        Status <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-toggle-on input-icon"></i>
                                        <select class="form-input-modern @error('status') is-invalid @enderror"
                                                id="status"
                                                name="status"
                                                required>
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('status', $slider->status) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $slider->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="sort_order" class="form-label-modern">
                                        Sort Order
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-sort-numeric-down input-icon"></i>
                                        <input type="text"
                                               class="form-input-modern @error('sort_order') is-invalid @enderror"
                                               id="sort_order"
                                               name="sort_order"
                                               value="{{ old('sort_order', $slider->sort_order ?? null) }}"
                                               placeholder="Auto-assigned if empty">
                                    </div>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        Current order: {{ $slider->sort_order }}
                                    </div>
                                    @error('sort_order')
                                        <div class="form-error">
                                            <i class="fas fa-exclamation-circle"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current Image -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">Current Image</label>
                            <div class="image-preview">
                                <img src="{{ $slider->thumbnail_url ?? $slider->image_url }}" alt="{{ $slider->heading }}" class="image-preview__img">
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Current image will be replaced if you upload a new one
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">
                                Update Image
                            </label>
                            
                            <x-image-requirements type="slider" />
                            
                            <div class="file-upload-wrapper">
                                <input type="file"
                                       class="file-upload-input @error('image') is-invalid @enderror"
                                       id="image"
                                       name="image"
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <label for="image" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Choose New Image</span>
                                </label>
                            </div>
                            @error('image')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <x-image-preview label="New Image Preview" />

                        <!-- Button Configuration -->
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-mouse-pointer"></i>
                                Button Configuration (Max 2 Buttons)
                            </label>

                            @php
                                $button1 = old('button_1_name') ? ['name' => old('button_1_name'), 'url' => old('button_1_url')] : ($slider->buttons[0] ?? ['name' => '', 'url' => '']);
                                $button2 = old('button_2_name') ? ['name' => old('button_2_name'), 'url' => old('button_2_url')] : ($slider->buttons[1] ?? ['name' => '', 'url' => '']);
                            @endphp

                            <!-- Button 1 -->
                            <div class="modern-card" style="margin-bottom: 1rem; padding: 1rem;">
                                <h6 style="margin-bottom: 1rem; color: var(--text-primary);">
                                    <i class="fas fa-1"></i> Button 1
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="button_1_name" class="form-label-modern">Button Name</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-tag input-icon"></i>
                                                <input type="text"
                                                       class="form-input-modern @error('button_1_name') is-invalid @enderror"
                                                       id="button_1_name"
                                                       name="button_1_name"
                                                       value="{{ $button1['name'] }}"
                                                       placeholder="e.g., Shop Now, Learn More">
                                            </div>
                                            @error('button_1_name')
                                                <div class="form-error">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @include('components.smart-link-selector', [
                                            'name' => 'button_1_url',
                                            'id' => 'button_1_url',
                                            'label' => 'Button URL',
                                            'value' => old('button_1_url', $button1['url'] ?? ''),
                                            'required' => false,
                                            'categories' => $categories ?? collect(),
                                            'bundles' => $bundles ?? collect(),
                                            'pages' => $pages ?? collect(),
                                        ])
                                        @error('button_1_url')
                                            <div class="form-error">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Button 2 -->
                            <div class="modern-card" style="padding: 1rem;">
                                <h6 style="margin-bottom: 1rem; color: var(--text-primary);">
                                    <i class="fas fa-2"></i> Button 2
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="button_2_name" class="form-label-modern">Button Name</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-tag input-icon"></i>
                                                <input type="text"
                                                       class="form-input-modern @error('button_2_name') is-invalid @enderror"
                                                       id="button_2_name"
                                                       name="button_2_name"
                                                       value="{{ $button2['name'] }}"
                                                       placeholder="e.g., Contact Us, View Details">
                                            </div>
                                            @error('button_2_name')
                                                <div class="form-error">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        @include('components.smart-link-selector', [
                                            'name' => 'button_2_url',
                                            'id' => 'button_2_url',
                                            'label' => 'Button URL',
                                            'value' => old('button_2_url', $button2['url'] ?? ''),
                                            'required' => false,
                                            'categories' => $categories ?? collect(),
                                            'bundles' => $bundles ?? collect(),
                                            'pages' => $pages ?? collect(),
                                        ])
                                        @error('button_2_url')
                                            <div class="form-error">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-hint" style="margin-top: 1rem;">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Both button name and URL are required for a button to be created. Leave both empty to remove a button.
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg btn-ripple">
                                <i class="fas fa-save"></i>
                                Update Slider
                            </button>
                            <a href="{{ route('admin.sliders.show', $slider) }}" class="btn btn-outline-info btn-lg">
                                <i class="fas fa-eye"></i>
                                View Slider
                            </a>
                            <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar - Live Preview -->
        <div class="col-lg-4">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-eye"></i>
                        Live Preview
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div id="sliderPreview" class="position-relative bg-light rounded" style="min-height: 200px; border: 2px dashed var(--border-color); background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url({{ $slider->image_url }}); background-size: cover; background-position: center;" data-original-image="{{ $slider->image_url }}">
                        <!-- Preview Content -->
                        <div id="previewContent" class="position-absolute top-50 start-50 translate-middle text-center w-100">
                            <h3 id="previewHeading" class="text-white mb-2" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">{{ $slider->heading }}</h3>
                            <p id="previewSubHeading" class="text-white-50 mb-3" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5); {{ $slider->sub_heading ? '' : 'display: none;' }}">{{ $slider->sub_heading }}</p>
                            <div id="previewButtons" class="d-flex gap-2 justify-content-center flex-wrap">
                                @if($slider->has_buttons)
                                    @foreach($slider->buttons as $index => $button)
                                        <a href="#" class="preview-button {{ $index === 0 ? '' : 'secondary' }}">{{ $button['name'] }}</a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card modern-card--glass" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-lightbulb"></i>
                        Tips
                    </h3>
                </div>
                <div class="modern-card__body">
                    <ul class="tips-list">
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Clear Heading</strong>
                                <p>Use a compelling heading to grab attention</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Quality Images</strong>
                                <p>Use high-resolution images (1920x600px recommended)</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Call-to-Action</strong>
                                <p>Add buttons to guide users to important pages</p>
                            </div>
                        </li>
                        <li class="tips-list__item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Sort Order</strong>
                                <p>Lower numbers appear first in the slider</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/admin-slider-preview.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof AdminSliderPreview !== 'undefined') {
        AdminSliderPreview.init({
            originalImageUrl: '{{ $slider->image_url }}'
        });
    }
});
</script>
@endpush
@endsection
