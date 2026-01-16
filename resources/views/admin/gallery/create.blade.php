@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Gallery
                </h1>
                <p class="page-header__subtitle">Create a new gallery</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Galleries</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card modern-card--glass">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Gallery Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.galleries.store') }}" class="modern-form" id="galleryForm">
                        @csrf

                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">
                                Gallery Name <span class="required">*</span>
                            </label>
                            <input type="text"
                                   class="form-input-modern @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g., Product Showcase"
                                   required>
                            @error('name')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="description" class="form-label-modern">
                                Description
                            </label>
                            <textarea class="form-input-modern @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="4"
                                      placeholder="Enter gallery description...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="category" class="form-label-modern">
                                Category <span class="required">*</span>
                            </label>
                            <select class="form-input-modern @error('category') is-invalid @enderror"
                                    id="category"
                                    name="category"
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="status" class="form-label-modern">
                                Status
                            </label>
                            <select class="form-input-modern @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status">
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-icon">
                                <i class="fas fa-save"></i>
                                <span>Create Gallery</span>
                            </button>
                            <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary btn-icon">
                                <i class="fas fa-times"></i>
                                <span>Cancel</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @include('admin.gallery.partials.tips')
        </div>
    </div>
</div>

<style>
.form-input-modern {
    padding-left: 1rem !important;
}
</style>
@endsection
