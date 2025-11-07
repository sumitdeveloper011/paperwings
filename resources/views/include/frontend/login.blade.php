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
                                    <input type="password" id="loginPassword" name="password" class="login-form-input form-input @error('password') is-invalid @enderror" placeholder="Enter your password" required>
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
                                
                                <div class="login-footer">
                                    <p>
                                        Don't have an account? 
                                        <a href="{{ route('register') }}">Sign up</a>
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