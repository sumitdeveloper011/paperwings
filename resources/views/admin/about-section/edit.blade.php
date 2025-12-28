@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit About Section
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.about-sections.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
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
                    <form method="POST" action="{{ route('admin.about-sections.update', $aboutSection) }}" class="modern-form" enctype="multipart/form-data">
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
                                      rows="4"
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
                                <div class="form-group-modern">
                                    <label for="button_link" class="form-label-modern">Button Link</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-link input-icon"></i>
                                        <input type="text" 
                                               class="form-input-modern @error('button_link') is-invalid @enderror" 
                                               id="button_link" 
                                               name="button_link" 
                                               value="{{ old('button_link', $aboutSection->button_link) }}" 
                                               placeholder="/about-us or https://example.com">
                                    </div>
                                    @error('button_link')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="sort_order" class="form-label-modern">Sort Order</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-sort input-icon"></i>
                                        <input type="number" 
                                               class="form-input-modern @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" 
                                               name="sort_order" 
                                               value="{{ old('sort_order', $aboutSection->sort_order) }}" 
                                               min="0">
                                    </div>
                                    @error('sort_order')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                    @error('status')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update About Section
                            </button>
                            <a href="{{ route('admin.about-sections.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

