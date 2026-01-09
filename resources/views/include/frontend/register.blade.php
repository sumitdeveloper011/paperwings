@extends('layouts.frontend.main')
@section('content')
    <section class="register-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9">
                    <div class="register-card">
                        <!-- Decorative background element -->
                        <div class="register-card__decoration-top"></div>
                        <div class="register-card__decoration-bottom"></div>

                        <div class="register-card__content">
                            <div class="register-header">
                                <div class="register-icon-badge">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <h2 class="register-title">Create Your Account</h2>
                                <p class="register-subtitle">Join us today and start your journey</p>
                            </div>

                            <form id="registerForm" class="register-form" action="{{ route('register.store') }}" method="POST">
                                @csrf
                                @if(session('error'))
                                    <div class="alert alert-danger mb-4" style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 4px; border: 1px solid #f5c6cb;">
                                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                    </div>
                                @endif
                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="form-group">
                                            <label for="registerFirstName" class="register-form-label">
                                                <i class="fas fa-user"></i>First Name
                                            </label>
                                            <input type="text" id="registerFirstName" name="first_name" value="{{ old('first_name') }}" class="register-form-input @error('first_name') is-invalid @enderror" placeholder="John" required>
                                            @error('first_name')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="registerLastName" class="register-form-label">
                                                <i class="fas fa-user"></i>Last Name
                                            </label>
                                            <input type="text" id="registerLastName" name="last_name" value="{{ old('last_name') }}" class="register-form-input @error('last_name') is-invalid @enderror" placeholder="Doe" required>
                                            @error('last_name')
                                            <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="registerEmail" class="register-form-label">
                                        <i class="fas fa-envelope"></i>Email Address
                                    </label>
                                    <input type="email" id="registerEmail" name="email" value="{{ old('email') }}" class="register-form-input @error('email') is-invalid @enderror" placeholder="john.doe@example.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="form-group">
                                            <label for="registerPassword" class="register-form-label">
                                                <i class="fas fa-lock"></i>Password
                                            </label>
                                            <div class="password-input-wrapper">
                                                <input type="password" id="registerPassword" name="password" class="register-form-input register-form-input--password @error('password') is-invalid @enderror" placeholder="Create a strong password" required>
                                                <button type="button" id="togglePassword" class="password-toggle-btn">
                                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                            <div id="passwordStrength" class="password-strength">
                                                <div id="passwordStrengthBar" class="password-strength-bar"></div>
                                            </div>
                                            <small id="passwordHint" class="password-hint"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="registerConfirmPassword" class="register-form-label">
                                                <i class="fas fa-lock"></i>Confirm Password
                                            </label>
                                            <div class="password-input-wrapper">
                                                <input type="password" id="registerConfirmPassword" name="password_confirmation" class="register-form-input register-form-input--password @error('password_confirmation') is-invalid @enderror" placeholder="Re-enter your password" required>
                                                <button type="button" id="toggleConfirmPassword" class="password-toggle-btn">
                                                    <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                                </button>
                                            </div>
                                            <small id="passwordMatch" class="password-match"></small>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="register-checkbox-label" for="agreeTerms">
                                        <input type="checkbox" id="agreeTerms" name="agreeTerms" value="1" {{ old('agreeTerms') ? 'checked' : '' }} required>
                                        <span>
                                            I agree to the <a href="{{ route('page.show', 'terms-and-conditions') }}" target="_blank">Terms & Conditions</a> and <a href="{{ route('page.show', 'privacy-policy') }}" target="_blank">Privacy Policy</a>
                                        </span>
                                    </label>
                                    @error('agreeTerms')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button type="submit" class="register-submit-btn">
                                    <i class="fas fa-user-plus"></i>Create Account
                                </button>

                                @if($googleLoginEnabled || $facebookLoginEnabled)
                                <!-- Social Login Divider -->
                                <div class="social-login-divider">
                                    <span>Or sign up with</span>
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

                                <div class="register-footer">
                                    <p>
                                        Already have an account?
                                        <a href="{{ route('login') }}">Sign in here</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
