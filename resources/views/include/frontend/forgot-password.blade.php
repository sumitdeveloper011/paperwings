@extends('layouts.frontend.main')
@section('content')
    <section class="forgot-password-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="forgot-password-card">
                        <!-- Decorative background element -->
                        <div class="forgot-password-card__decoration-top"></div>
                        <div class="forgot-password-card__decoration-bottom"></div>

                        <div class="forgot-password-card__content">
                            <div class="forgot-password-header">
                                <div class="forgot-password-icon-badge">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <h2 class="forgot-password-title">Forgot Password?</h2>
                                <p class="forgot-password-subtitle">Enter your email address and we'll send you a link to reset your password.</p>
                            </div>

                            @if(session('success'))
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <p class="mb-2"><strong>{{ session('success') }}</strong></p>
                                    <p class="mb-0 small">Please check your inbox and spam folder. The reset link may take a few minutes to arrive. If you don't receive it within 10 minutes, please try again.</p>
                                </div>
                            </div>
                            @endif

                            <form id="forgotPasswordForm" class="forgot-password-form" action="{{ route('password.email') }}" method="POST" novalidate>
                                @csrf
                                <div class="form-group mb-4">
                                    <label for="forgotEmail" class="forgot-password-form-label">
                                        <i class="fas fa-envelope"></i>Email Address
                                    </label>
                                    <input type="email" id="forgotEmail" name="email" value="{{ old('email') }}" class="forgot-password-form-input form-input @error('email') is-invalid @enderror" placeholder="Enter your email address" required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button type="submit" class="forgot-password-submit-btn">
                                    <i class="fas fa-paper-plane"></i>Send Reset Link
                                </button>

                                <div class="forgot-password-back-link">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.FormSubmissionHandler) {
        window.FormSubmissionHandler.init('forgotPasswordForm', {
            loadingText: 'Sending Reset Link...',
            timeout: 10000
        });
    }
});
</script>
@endpush
@endsection
