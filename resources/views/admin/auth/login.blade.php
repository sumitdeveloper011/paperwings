@extends('layouts.admin.auth')

@section('content')
<script>
    // Add classes to body and html to prevent scrolling
    document.addEventListener('DOMContentLoaded', function() {
        document.body.classList.add('login-page-body');
        document.documentElement.classList.add('login-page-html');

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            document.body.classList.remove('login-page-body');
            document.documentElement.classList.remove('login-page-html');
        });

        // Password toggle functionality
        const passwordInput = document.getElementById('adminPassword');
        const toggleButton = document.getElementById('toggleAdminPassword');
        const passwordIcon = document.getElementById('adminPasswordIcon');

        if (toggleButton && passwordInput && passwordIcon) {
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle icon
                if (type === 'text') {
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                    toggleButton.setAttribute('aria-label', 'Hide password');
                } else {
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                    toggleButton.setAttribute('aria-label', 'Show password');
                }
            });
        }

        // CSRF Token Auto-Refresh - Prevent 419 errors
        // Update CSRF token from meta tag to form before submission
        const loginForm = document.querySelector('.login-form');
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        if (loginForm && csrfToken) {
            // Ensure form has latest CSRF token before submission
            loginForm.addEventListener('submit', function(e) {
                const csrfInput = loginForm.querySelector('input[name="_token"]');
                if (csrfInput && csrfToken) {
                    csrfInput.value = csrfToken.getAttribute('content');
                }
            });

            // Refresh page token every 30 minutes to keep session alive
            // This helps prevent token expiration when form is open for long time
            setInterval(function() {
                // Just reload the page token by fetching current page
                // This keeps the session alive
                fetch(window.location.href, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                }).catch(() => {
                    // Silently fail - user can still refresh manually
                });
            }, 30 * 60 * 1000); // 30 minutes
        }
    });
</script>

    <div class="login-page">
        <!-- Left Panel -->
        <div class="login-page__left-panel">
            <div class="login-page__brand">
                <img src="{{ site_logo() }}" alt="PAPERWINGS" class="login-page__logo">
                <span class="login-page__brand-text">PAPERWINGS</span>
            </div>

            <div class="login-page__content">
                <h1 class="login-page__title">{{ __('Welcome Back!') }}</h1>
                <p class="login-page__subtitle">{{ __('Sign in to continue your journey with us.') }}</p>
            </div>

            <a href="" class="login-page__home-link">
                <i class="fas fa-chevron-left"></i>
                {{ __('HOME') }}
            </a>
        </div>

        <!-- Right Panel -->
        <div class="login-page__right-panel">
            <div class="login-page__header">
                <div></div>
            </div>

            <div class="login-page__copyright">
                © Copyright {{ date('Y') }} PAPERWINGS.
            </div>
        </div>

        <!-- Login Form -->
        <form class="login-form" method="POST" action="{{ route('admin.authenticate') }}">
            @csrf
            <h2 class="login-form__title">{{ __('LOGIN') }}</h2>

            <div class="login-form__group {{ $errors->has('email') ? 'has-error' : '' }}">
                <input type="email" name="email" class="login-form__input {{ $errors->has('email') ? 'error' : '' }}" placeholder="Email" value="{{ old('email') }}">
                <i class="fas fa-envelope login-form__icon"></i>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="login-form__group {{ $errors->has('password') ? 'has-error' : '' }}">
                <input type="password" name="password" id="adminPassword" class="login-form__input {{ $errors->has('password') ? 'error' : '' }}" placeholder="Password">
                <i class="fas fa-lock login-form__icon"></i>
                <button type="button" id="toggleAdminPassword" class="login-form__password-toggle" aria-label="Show password">
                    <i class="fas fa-eye" id="adminPasswordIcon"></i>
                </button>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="login-form__btn">Sign In</button>
        </form>
    </div>
@endsection
