@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit FAQ
                </h1>
                <p class="page-header__subtitle">Update FAQ information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to FAQs</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="modern-form" id="faqForm" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

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
                            <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('question') is-invalid @enderror"
                                   id="question"
                                   name="question"
                                   value="{{ old('question', $faq->question) }}"
                                   placeholder="Enter question">
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="answer" class="form-label">Answer <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('answer') is-invalid @enderror"
                                      id="answer"
                                      name="answer"
                                      rows="6"
                                      placeholder="Enter answer">{{ old('answer', $faq->answer) }}</textarea>
                            @error('answer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text"
                                   class="form-control @error('category') is-invalid @enderror"
                                   id="category"
                                   name="category"
                                   value="{{ old('category', $faq->category) }}"
                                   placeholder="e.g., General, Shipping, Returns">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Leave empty for General category
                            </small>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="modern-card mb-4">
                    <div class="modern-card__header">
                        <h3 class="modern-card__title">
                            <i class="fas fa-cog"></i>
                            Additional Settings
                        </h3>
                    </div>
                    <div class="modern-card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number"
                                           class="form-control @error('sort_order') is-invalid @enderror"
                                           id="sort_order"
                                           name="sort_order"
                                           value="{{ old('sort_order', $faq->sort_order ?? 0) }}"
                                           min="0"
                                           placeholder="0">
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        Lower numbers appear first. Leave 0 for auto-ordering.
                                    </small>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status">
                                        <option value="">Select Status</option>
                                        <option value="1" {{ old('status', $faq->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $faq->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Update FAQ
                    </button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-lg" style="background-color: #f8f9fa;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.faq.partials.tips')
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/ckeditor-custom.js') }}"></script>
@include('components.ckeditor', [
    'id' => 'answer',
    'uploadUrl' => route('admin.pages.uploadImage'),
    'toolbar' => 'full'
])

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('faqForm');
    if (form) {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('invalid', function(ev) {
                ev.preventDefault();
                ev.stopPropagation();
            }, true);
        });
    }
});
</script>
@endpush
@endsection
