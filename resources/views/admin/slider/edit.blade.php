@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="content-title">Edit Slider</h1>
                <p class="content-subtitle">Update slider: {{ $slider->heading }}</p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.sliders.show', $slider) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye"></i> View Slider
                    </a>
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sliders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data" id="sliderForm">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Left Column - Main Slider Information -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Slider Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="heading" class="form-label">Heading <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('heading') is-invalid @enderror" 
                                               id="heading" name="heading" value="{{ old('heading', $slider->heading) }}" required>
                                        @error('heading')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', $slider->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $slider->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="sub_heading" class="form-label">Sub Heading</label>
                                <input type="text" class="form-control @error('sub_heading') is-invalid @enderror" 
                                       id="sub_heading" name="sub_heading" value="{{ old('sub_heading', $slider->sub_heading) }}">
                                @error('sub_heading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $slider->sort_order) }}" min="1">
                                <small class="form-text text-muted">Current order: {{ $slider->sort_order }}</small>
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Current Image -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-image me-2"></i>Current Image
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="{{ $slider->image_url }}" alt="{{ $slider->heading }}" 
                                     class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                            </div>
                            <p class="text-muted text-center mb-0">
                                <small>Current image will be replaced if you upload a new one</small>
                            </p>
                        </div>
                    </div>

                    <!-- Update Image -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-camera me-2"></i>Update Image
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="image" class="form-label">Upload New Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                <small class="form-text text-muted">Leave empty to keep current image. Recommended size: 1920x800px. Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB.</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div id="imagePreview" class="text-center" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                                        <i class="fas fa-times"></i> Remove New Image
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button Configuration -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-mouse-pointer me-2"></i>Button Configuration (Max 2 Buttons)
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $button1 = old('button_1_name') ? ['name' => old('button_1_name'), 'url' => old('button_1_url')] : ($slider->first_button ?? ['name' => '', 'url' => '']);
                                $button2 = old('button_2_name') ? ['name' => old('button_2_name'), 'url' => old('button_2_url')] : ($slider->second_button ?? ['name' => '', 'url' => '']);
                            @endphp

                            <!-- Button 1 -->
                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3">
                                    <i class="fas fa-1 me-2"></i>Button 1
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="button_1_name" class="form-label">Button Name</label>
                                            <input type="text" class="form-control @error('button_1_name') is-invalid @enderror" 
                                                   id="button_1_name" name="button_1_name" value="{{ $button1['name'] }}"
                                                   placeholder="e.g., Shop Now, Learn More">
                                            @error('button_1_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="button_1_url" class="form-label">Button URL</label>
                                            <input type="url" class="form-control @error('button_1_url') is-invalid @enderror" 
                                                   id="button_1_url" name="button_1_url" value="{{ $button1['url'] }}"
                                                   placeholder="https://example.com">
                                            @error('button_1_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Button 2 -->
                            <div class="border rounded p-3">
                                <h6 class="mb-3">
                                    <i class="fas fa-2 me-2"></i>Button 2
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="button_2_name" class="form-label">Button Name</label>
                                            <input type="text" class="form-control @error('button_2_name') is-invalid @enderror" 
                                                   id="button_2_name" name="button_2_name" value="{{ $button2['name'] }}"
                                                   placeholder="e.g., Contact Us, View Details">
                                            @error('button_2_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="button_2_url" class="form-label">Button URL</label>
                                            <input type="url" class="form-control @error('button_2_url') is-invalid @enderror" 
                                                   id="button_2_url" name="button_2_url" value="{{ $button2['url'] }}"
                                                   placeholder="https://example.com">
                                            @error('button_2_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Both button name and URL are required for a button to be created. Leave both empty to remove a button.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Preview & Actions -->
                <div class="col-lg-4">
                    <!-- Live Preview -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-eye me-2"></i>Live Preview
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="sliderPreview" class="position-relative bg-light rounded" style="min-height: 200px; background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url({{ $slider->image_url }}); background-size: cover; background-position: center;">
                                <!-- Preview Content -->
                                <div id="previewContent" class="position-absolute top-50 start-50 translate-middle text-center w-100">
                                    <h3 id="previewHeading" class="text-white mb-2">{{ $slider->heading }}</h3>
                                    <p id="previewSubHeading" class="text-white-50 mb-3" style="{{ $slider->sub_heading ? '' : 'display: none;' }}">{{ $slider->sub_heading }}</p>
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

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-save me-2"></i>Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Slider
                                </button>
                                <a href="{{ route('admin.sliders.show', $slider) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-2"></i>View Slider
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Reset Changes
                                </button>
                                <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
#sliderPreview {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    transition: all 0.3s ease;
}

.preview-button {
    background-color: rgba(0, 123, 255, 0.8);
    border: 2px solid #007bff;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.preview-button:hover {
    background-color: #007bff;
    color: white;
    text-decoration: none;
}

.preview-button.secondary {
    background-color: rgba(108, 117, 125, 0.8);
    border-color: #6c757d;
}

.preview-button.secondary:hover {
    background-color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');
    const sliderPreview = document.getElementById('sliderPreview');
    
    // Form inputs for live preview
    const headingInput = document.getElementById('heading');
    const subHeadingInput = document.getElementById('sub_heading');
    const button1NameInput = document.getElementById('button_1_name');
    const button1UrlInput = document.getElementById('button_1_url');
    const button2NameInput = document.getElementById('button_2_name');
    const button2UrlInput = document.getElementById('button_2_url');

    // Image preview functionality
    imageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                
                // Update slider preview background
                sliderPreview.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url(${e.target.result})`;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Remove image functionality
    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.style.display = 'none';
        // Reset to original image
        sliderPreview.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url({{ $slider->image_url }})`;
    });

    // Live preview updates
    function updatePreview() {
        const heading = headingInput.value.trim();
        const subHeading = subHeadingInput.value.trim();
        const button1Name = button1NameInput.value.trim();
        const button1Url = button1UrlInput.value.trim();
        const button2Name = button2NameInput.value.trim();
        const button2Url = button2UrlInput.value.trim();

        // Update heading
        const previewHeading = document.getElementById('previewHeading');
        previewHeading.textContent = heading || 'Your Heading Here';
        
        // Update sub heading
        const previewSubHeading = document.getElementById('previewSubHeading');
        if (subHeading) {
            previewSubHeading.textContent = subHeading;
            previewSubHeading.style.display = 'block';
        } else {
            previewSubHeading.style.display = 'none';
        }
        
        // Update buttons
        const previewButtons = document.getElementById('previewButtons');
        previewButtons.innerHTML = '';
        
        if (button1Name && button1Url) {
            const btn1 = document.createElement('a');
            btn1.href = '#';
            btn1.className = 'preview-button';
            btn1.textContent = button1Name;
            previewButtons.appendChild(btn1);
        }
        
        if (button2Name && button2Url) {
            const btn2 = document.createElement('a');
            btn2.href = '#';
            btn2.className = 'preview-button secondary';
            btn2.textContent = button2Name;
            previewButtons.appendChild(btn2);
        }
    }

    // Add event listeners for live preview
    [headingInput, subHeadingInput, button1NameInput, button1UrlInput, button2NameInput, button2UrlInput].forEach(input => {
        if (input) {
            input.addEventListener('input', updatePreview);
        }
    });

    // Button validation
    function validateButtons() {
        const button1Name = button1NameInput.value.trim();
        const button1Url = button1UrlInput.value.trim();
        const button2Name = button2NameInput.value.trim();
        const button2Url = button2UrlInput.value.trim();

        // If button 1 name is filled, URL is required
        if (button1Name && !button1Url) {
            button1UrlInput.setCustomValidity('URL is required when button name is provided');
        } else {
            button1UrlInput.setCustomValidity('');
        }

        // If button 1 URL is filled, name is required
        if (button1Url && !button1Name) {
            button1NameInput.setCustomValidity('Button name is required when URL is provided');
        } else {
            button1NameInput.setCustomValidity('');
        }

        // If button 2 name is filled, URL is required
        if (button2Name && !button2Url) {
            button2UrlInput.setCustomValidity('URL is required when button name is provided');
        } else {
            button2UrlInput.setCustomValidity('');
        }

        // If button 2 URL is filled, name is required
        if (button2Url && !button2Name) {
            button2NameInput.setCustomValidity('Button name is required when URL is provided');
        } else {
            button2NameInput.setCustomValidity('');
        }
    }

    // Add validation event listeners
    [button1NameInput, button1UrlInput, button2NameInput, button2UrlInput].forEach(input => {
        if (input) {
            input.addEventListener('blur', validateButtons);
        }
    });

    // Form submission validation
    document.getElementById('sliderForm').addEventListener('submit', function(e) {
        validateButtons();
        
        // Check if form is valid
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        this.classList.add('was-validated');
    });
});

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All changes will be lost.')) {
        location.reload();
    }
}
</script>
@endsection
