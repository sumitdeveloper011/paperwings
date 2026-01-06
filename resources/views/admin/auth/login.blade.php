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
                <input type="password" name="password" class="login-form__input {{ $errors->has('password') ? 'error' : '' }}" placeholder="Password">
                <i class="fas fa-lock login-form__icon"></i>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="login-form__btn">Sign In</button>
        </form>
    </div>
@endsection
