@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-edit"></i>
                    Edit Coupon
                </h1>
                <p class="page-header__subtitle">Update coupon information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Coupons</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="modern-form" id="couponForm" novalidate>
                @csrf
                @method('PUT')

                @include('admin.coupon.partials.form', ['coupon' => $coupon])

                <!-- Form Actions -->
                <div class="form-actions" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        Update Coupon
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-lg" style="background-color: #f8f9fa;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @include('admin.coupon.partials.tips')
        </div>
    </div>
</div>

@include('admin.coupon.partials.scripts')

<!-- CKEditor 5 - Custom build with SourceEditing -->
<script src="{{ asset('assets/js/ckeditor-custom.js') }}"></script>

<!-- CKEditor Component for Description -->
@include('components.ckeditor', [
    'id' => 'description',
    'uploadUrl' => route('admin.pages.uploadImage'),
    'toolbar' => 'full'
])
@endsection
