@extends('layouts.frontend.main')
@section('content')
<section class="page-header">
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>/</span>
            <a href="{{ route('account.view-profile') }}">My Account</a>
            <span>/</span>
            <span>Change Password</span>
        </div>
        <h1 class="page-title">My Account</h1>
    </div>
</section>
<section class="account-section">
    <div class="container">
        <div class="row">
            @include('frontend.account.partials.sidebar')

            <!-- Account Content -->
            <div class="col-lg-9">
                <!-- Change Password -->
                <div class="account-content">
                    <div class="account-block">
                        <h2 class="account-block__title">Change Password</h2>

                        <form class="account-form" id="changePasswordForm" method="POST" action="{{ route('account.update-password') }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="currentPassword" class="form-label">Current Password <span class="required">*</span></label>
                                <input type="password" id="currentPassword" name="current_password" class="form-input" placeholder="Enter your current password" required>
                                <div class="form-help">
                                    <i class="fas fa-info-circle"></i>
                                    You must enter your current password to change it
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="newPassword" class="form-label">New Password <span class="required">*</span></label>
                                <input type="password" id="newPassword" name="password" class="form-input" placeholder="Enter your new password" required>
                                <div class="form-help">
                                    <i class="fas fa-info-circle"></i>
                                    Password must be at least 8 characters long
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirmPassword" class="form-label">Confirm New Password <span class="required">*</span></label>
                                <input type="password" id="confirmPassword" name="password_confirmation" class="form-input" placeholder="Confirm your new password" required>
                            </div>

                            <div class="account-form__actions">
                                <button type="submit" class="btn btn-primary">Update Password</button>
                                <a href="{{ route('account.view-profile') }}" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
