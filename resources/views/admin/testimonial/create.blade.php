@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-plus-circle"></i>
                    Add Testimonial
                </h1>
                <p class="page-header__subtitle">Create a new customer testimonial</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Testimonials</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.testimonials.store') }}" class="modern-form" id="testimonialForm" enctype="multipart/form-data" novalidate>
                @csrf

                @include('admin.testimonial.partials.form')

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Create Testimonial
                    </button>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary btn-lg" style="background-color: #f8f9fa;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.testimonial.partials.tips')
        </div>
    </div>
</div>

<!-- CKEditor 5 - Custom build with SourceEditing -->
<script src="{{ asset('assets/js/ckeditor-custom.js') }}"></script>

<!-- CKEditor Component for Review -->
@include('components.ckeditor', [
    'id' => 'review',
    'uploadUrl' => route('admin.pages.uploadImage'),
    'toolbar' => 'full'
])
@endsection
