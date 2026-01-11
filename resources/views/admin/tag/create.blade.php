@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Tag
                </h1>
                <p class="page-header__subtitle">Create a new product tag</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Tags</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.tags.store') }}" class="modern-form" id="tagForm" novalidate>
                @csrf

                <!-- Basic Information -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tag Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="Enter tag name"
                                   required
                                   minlength="2"
                                   maxlength="255">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Tag name must be unique. Slug will be auto-generated from the name.
                            </small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Create Tag
                    </button>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary btn-lg" style="background-color: #f8f9fa;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.tag.partials.tips')
        </div>
    </div>
</div>

<script>
// Auto-generate slug from name (for display purposes)
document.getElementById('name').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9 -]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    // Slug is auto-generated in model
});
</script>
@endsection
