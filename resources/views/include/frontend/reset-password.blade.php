@extends('layouts.frontend.main')
@section('content')
<section class="reset-password-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="reset-password-card">
                    <!-- Decorative background element -->
                    <div class="reset-password-card__decoration-top"></div>
                    <div class="reset-password-card__decoration-bottom"></div>
                    
                    <div class="reset-password-card__content">
                        <div class="reset-password-header">
                            <div class="reset-password-icon-badge">
                                <i class="fas fa-key"></i>
                            </div>
                            <h2 class="reset-password-title">Reset Password</h2>
                            <p class="reset-password-subtitle">Enter your new password below. Make sure it's strong and secure.</p>
                        </div>
                        
                        <form id="resetPasswordForm" class="reset-password-form" action="{{ route('password.update') }}" method="POST" novalidate>
                            @csrf
                            <input type="hidden" name="token" value="{{ $token ?? old('token') }}">
                            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                            
                            <div class="form-group mb-4">
                                <label for="resetPassword" class="reset-password-form-label">
                                    <i class="fas fa-lock"></i>New Password
                                </label>
                                <div class="password-input-wrapper">
                                    <input type="password" id="resetPassword" name="password" class="reset-password-form-input reset-password-form-input--password @error('password') is-invalid @enderror" placeholder="Enter your new password" required>
                                    <button type="button" id="toggleResetPassword" class="password-toggle-btn">
                                        <i class="fas fa-eye" id="resetPasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback" style="display: block;">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="invalid-feedback" id="resetPasswordError"></div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="resetConfirmPassword" class="reset-password-form-label">
                                    <i class="fas fa-lock"></i>Confirm Password
                                </label>
                                <div class="password-input-wrapper">
                                    <input type="password" id="resetConfirmPassword" name="password_confirmation" class="reset-password-form-input reset-password-form-input--password @error('password_confirmation') is-invalid @enderror" placeholder="Re-enter your new password" required>
                                    <button type="button" id="toggleResetConfirmPassword" class="password-toggle-btn">
                                        <i class="fas fa-eye" id="resetConfirmPasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="invalid-feedback" style="display: block;">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                                <div class="invalid-feedback" id="resetConfirmPasswordError"></div>
                            </div>
                            
                            <button type="submit" class="reset-password-submit-btn">
                                <i class="fas fa-check-circle"></i>Reset Password
                            </button>
                            
                            <div class="reset-password-back-link">
                                <a href="{{ route('login') }}">
                                    <i class="fas fa-arrow-left"></i>Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection