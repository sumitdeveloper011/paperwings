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
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-icon">
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
                    <h3 class="modern-card__title">FAQ Information</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="modern-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group-modern">
                            <label for="question" class="form-label-modern">Question <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-question input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('question') is-invalid @enderror" 
                                       id="question" 
                                       name="question" 
                                       value="{{ old('question', $faq->question) }}" 
                                       placeholder="Enter question"
                                       required>
                            </div>
                            @error('question')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="answer" class="form-label-modern">Answer <span class="required">*</span></label>
                            <textarea class="form-input-modern @error('answer') is-invalid @enderror" 
                                      id="answer" 
                                      name="answer" 
                                      rows="6"
                                      placeholder="Enter answer" required>{{ old('answer', $faq->answer) }}</textarea>
                            @error('answer')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="category" class="form-label-modern">Category</label>
                            <div class="input-wrapper">
                                <i class="fas fa-tag input-icon"></i>
                                <input type="text" 
                                       class="form-input-modern @error('category') is-invalid @enderror" 
                                       id="category" 
                                       name="category" 
                                       value="{{ old('category', $faq->category) }}" 
                                       placeholder="e.g., General, Shipping, Returns">
                            </div>
                            <small class="form-text text-muted">Leave empty for General category</small>
                            @error('category')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="sort_order" class="form-label-modern">Sort Order</label>
                            <div class="input-wrapper">
                                <i class="fas fa-sort input-icon"></i>
                                <input type="number" 
                                       class="form-input-modern @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="{{ old('sort_order', $faq->sort_order ?? 0) }}" 
                                       min="0">
                            </div>
                            @error('sort_order')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label for="status" class="form-label-modern">Status <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-toggle-on input-icon"></i>
                                <select class="form-input-modern @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="1" {{ old('status', $faq->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $faq->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @error('status')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update FAQ
                            </button>
                            <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">
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

