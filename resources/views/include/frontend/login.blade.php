@extends('layouts.frontend.main')
@section('content')
    <section class="login-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="login-card">
                        <!-- Decorative background element -->
                        <div class="login-card__decoration-top"></div>
                        <div class="login-card__decoration-bottom"></div>
                        
                        <div class="login-card__content">
                            <div class="login-header">
                                <div class="login-icon-badge">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h2 class="login-title">Welcome Back</h2>
                                <p class="login-subtitle">Sign in to your account to continue</p>
                            </div>
                            
                            <form id="loginForm" class="login-form" action="{{ route('login.authenticate') }}" method="POST" novalidate>
                                @csrf
                                
                                <div class="form-group mb-4">
                                    <label for="loginEmail" class="login-form-label">
                                        <i class="fas fa-envelope"></i>Email Address
                                    </label>
                                    <input type="email" id="loginEmail" name="email" value="{{ old('email') }}" class="login-form-input form-input @error('email') is-invalid @enderror" placeholder="Enter your email" required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="form-group mb-4">
                                    <label for="loginPassword" class="login-form-label">
                                        <i class="fas fa-lock"></i>Password
                                    </label>
                                    <div class="password-input-wrapper">
                                        <input type="password" id="loginPassword" name="password" class="login-form-input form-input login-form-input--password @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                                        <button type="button" id="toggleLoginPassword" class="password-toggle-btn">
                                            <i class="fas fa-eye" id="loginPasswordIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                <div class="login-form-options">
                                    <label class="login-checkbox-label">
                                        <input type="checkbox" id="rememberMe" name="remember" value="1">
                                        <span>Remember me</span>
                                    </label>
                                    <a href="{{ route('forgot-password') }}" class="login-forgot-link">Forgot Password?</a>
                                </div>
                                
                                <button type="submit" class="login-submit-btn">
                                    <i class="fas fa-sign-in-alt"></i>Login
                                </button>

                                @if($googleLoginEnabled || $facebookLoginEnabled)
                                <!-- Social Login Divider -->
                                <div class="social-login-divider">
                                    <span>Or continue with</span>
                                </div>

                                <!-- Social Login Buttons -->
                                <div class="social-login-buttons">
                                    @if($googleLoginEnabled)
                                    <a href="{{ route('auth.google') }}" class="social-login-btn social-login-btn--google">
                                        <i class="fab fa-google"></i>
                                        <span>Google</span>
                                    </a>
                                    @endif
                                    @if($facebookLoginEnabled)
                                    <a href="{{ route('auth.facebook') }}" class="social-login-btn social-login-btn--facebook">
                                        <i class="fab fa-facebook-f"></i>
                                        <span>Facebook</span>
                                    </a>
                                    @endif
                                </div>
                                @endif
                                
                                <div class="login-footer">
                                    <p>
                                        Don't have an account? 
                                        <a href="{{ route('register') }}">Sign up</a>
                                    </p>
                                </div>
                            </form>

                            @if(session('unverified_email') || ($errors->has('email') && session('unverified_email')))
                            <div class="resend-verification-section mt-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <div>
                                        <p class="mb-2">Your email address is not verified. Please check your inbox or resend the verification email.</p>
                                        <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="email" value="{{ session('unverified_email') ?? old('email') }}">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-paper-plane"></i> Resend Verification Email
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
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
        window.FormSubmissionHandler.init('loginForm', {
            loadingText: 'Logging in...',
            timeout: 10000
        });
    }
});
</script>
@endpush
@endsection