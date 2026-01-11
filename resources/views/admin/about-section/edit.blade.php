@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-info-circle"></i>
                    About Section
                </h1>
                <p class="page-header__subtitle">Manage homepage about section widget</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">About Section Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.about-sections.update') }}" class="modern-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group-modern">
                            <label for="badge" class="form-label-modern">Badge</label>
                            <div class="input-wrapper">
                                <i class="fas fa-tag input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('badge') is-invalid @enderror"
                                       id="badge"
                                       name="badge"
                                       value="{{ old('badge', $aboutSection->badge) }}"
                                       placeholder="e.g., THE STATIONERO">
                            </div>
                            @error('badge')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="title" class="form-label-modern">Title <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-heading input-icon"></i>
                                <input type="text"
                                       class="form-input-modern @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $aboutSection->title) }}"
                                       placeholder="Enter section title"
                                       required>
                            </div>
                            @error('title')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="description" class="form-label-modern">Description</label>
                            <textarea class="form-input-modern @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="6"
                                      data-required="false"
                                      placeholder="Enter section description">{{ old('description', $aboutSection->description) }}</textarea>
                            @error('description')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="image" class="form-label-modern">Image</label>
                            @if($aboutSection->image_url)
                                <div class="mb-2">
                                    <img src="{{ $aboutSection->image_url }}" alt="{{ $aboutSection->title }}"
                                         class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                </div>
                            @endif
                            <input type="file"
                                   class="form-input-modern @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            <small class="form-text text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF, WEBP. Leave empty to keep current image.</small>
                            @error('image')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="button_text" class="form-label-modern">Button Text</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-mouse-pointer input-icon"></i>
                                        <input type="text"
                                               class="form-input-modern @error('button_text') is-invalid @enderror"
                                               id="button_text"
                                               name="button_text"
                                               value="{{ old('button_text', $aboutSection->button_text) }}"
                                               placeholder="e.g., Find Out More">
                                    </div>
                                    @error('button_text')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                @include('components.smart-link-selector', [
                                    'name' => 'button_link',
                                    'id' => 'button_link',
                                    'label' => 'Button Link',
                                    'value' => old('button_link', $aboutSection->button_link),
                                    'required' => false,
                                    'categories' => $categories ?? [],
                                    'products' => $products ?? [],
                                    'bundles' => $bundles ?? [],
                                    'pages' => []
                                ])
                                @error('button_link')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group-modern">
                            <label for="status" class="form-label-modern">Status <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select class="form-input-modern @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', $aboutSection->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $aboutSection->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <small class="form-text text-muted">When active, this section will be displayed on the homepage.</small>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update About Section
                            </button>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> This section appears on the homepage. For detailed company information, use the <a href="{{ route('admin.pages.index') }}" target="_blank">About Us page</a> in the Pages section.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor 5 - Custom build with SourceEditing -->
<script src="{{ asset('assets/js/ckeditor-custom.js') }}"></script>

<!-- CKEditor Component for Description -->
@include('components.ckeditor', [
    'id' => 'description',
    'uploadUrl' => route('admin.pages.uploadImage'),
    'toolbar' => 'full'
])
@endsection

