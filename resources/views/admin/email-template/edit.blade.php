@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Email Template
                </h1>
                <p class="page-header__subtitle">Update template: {{ $emailTemplate->name }}</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.email-templates.show', $emailTemplate) }}" class="btn btn-outline-info btn-icon">
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                </a>
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
                    <form method="POST" action="{{ route('admin.email-templates.update', $emailTemplate) }}" class="modern-form" id="templateForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group-modern">
                            <label for="name" class="form-label-modern">
                                Template Name <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <input type="text"
                                       class="form-input-modern @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $emailTemplate->name) }}"
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
                                       value="{{ old('slug', $emailTemplate->slug) }}"
                                       placeholder="order-confirmation">
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
                                        <option value="{{ $key }}" {{ old('category', $emailTemplate->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
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
                                       value="{{ old('subject', $emailTemplate->subject) }}"
                                       placeholder="e.g., Order Confirmation - {order_number}"
                                       required>
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
                                      placeholder="Brief description of this template">{{ old('description', $emailTemplate->description) }}</textarea>
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
                                      required>{{ old('body', $emailTemplate->body) }}</textarea>
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
                                    <option value="1" {{ old('is_active', $emailTemplate->is_active) == '1' || old('is_active', $emailTemplate->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $emailTemplate->is_active) == '0' || old('is_active', $emailTemplate->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
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
                                Update Template
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
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Template Info
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-item__label">Version</div>
                            <div class="detail-item__value">v{{ $emailTemplate->version }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Created</div>
                            <div class="detail-item__value">{{ $emailTemplate->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-item__label">Last Updated</div>
                            <div class="detail-item__value">{{ $emailTemplate->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @include('admin.email-template.partials.tips')

            @can('email-templates.edit')
            <div class="modern-card" style="margin-top: 1.5rem;">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.email-templates.duplicate', $emailTemplate) }}" style="margin-bottom: 1rem;">
                        @csrf
                        <button type="submit" class="btn btn-outline-info btn-block">
                            <i class="fas fa-copy"></i>
                            Duplicate Template
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.email-templates.sendTest', $emailTemplate) }}" id="testEmailForm">
                        @csrf
                        <div class="form-group-modern">
                            <input type="email" name="test_email" class="form-input-modern" placeholder="test@example.com" required>
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-paper-plane"></i>
                            Send Test Email
                        </button>
                    </form>
                </div>
            </div>
            @endcan
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
