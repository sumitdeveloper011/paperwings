@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Email Template
                </h1>
                <p class="page-header__subtitle">Create a new email template</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Templates</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Template Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.email-templates.store') }}" class="modern-form" id="templateForm">
                        @csrf

                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">
                                Template Name <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text"
                                       class="form-input-modern @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="e.g., Order Confirmation"
                                       required>
                            </div>
                            @error('name')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="slug" class="form-label-modern">
                                Slug
                            </label>
                            <div class="input-wrapper">
                                <input type="text"
                                       class="form-input-modern @error('slug') is-invalid @enderror"
                                       id="slug"
                                       name="slug"
                                       value="{{ old('slug') }}"
                                       placeholder="order-confirmation (auto-generated if empty)">
                            </div>
                            @error('slug')
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
                            <div class="input-wrapper">
                                <select class="form-input-modern @error('category') is-invalid @enderror"
                                        id="category"
                                        name="category"
                                        required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="subject" class="form-label-modern">
                                Email Subject <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text"
                                       class="form-input-modern @error('subject') is-invalid @enderror"
                                       id="subject"
                                       name="subject"
                                       value="{{ old('subject') }}"
                                       placeholder="e.g., Order Confirmation - {order_number}"
                                       required>
                            </div>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Use variables like {order_number}, {customer_name}, etc.
                            </div>
                            @error('subject')
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
                                      rows="3"
                                      style="resize: vertical;"
                                      placeholder="Brief description of this template">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="body" class="form-label-modern">
                                Email Body <span class="required">*</span>
                            </label>
                            <textarea class="form-input-modern @error('body') is-invalid @enderror"
                                      id="body"
                                      name="body"
                                      rows="25"
                                      required>{{ old('body') }}</textarea>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                Use variables like {order_number}, {customer_name}, {order_total}, etc. in curly braces.
                            </div>
                            @error('body')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="is_active" class="form-label-modern">
                                Status <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <select class="form-input-modern @error('is_active') is-invalid @enderror"
                                        id="is_active"
                                        name="is_active"
                                        required>
                                    <option value="">Select Status</option>
                                    <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('is_active')
                                <div class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i>
                                Create Template
                            </button>
                            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.email-template.partials.tips')
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/ckeditor-custom.js') }}"></script>
@include('components.ckeditor', [
    'id' => 'body',
    'uploadUrl' => null,
    'toolbar' => 'email'
])

<script>
document.getElementById('name').addEventListener('input', function(e) {
    const slugField = document.getElementById('slug');
    if (!slugField.value || slugField.dataset.autoGenerated === 'true') {
        const slug = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        slugField.value = slug;
        slugField.dataset.autoGenerated = 'true';
    }
});

document.getElementById('slug').addEventListener('input', function() {
    this.dataset.autoGenerated = 'false';
});
</script>
@endsection
