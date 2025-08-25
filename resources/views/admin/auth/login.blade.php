@extends('layouts.admin.auth')

@section('content')
    <div class="login-page">
        <!-- Left Panel -->
        <div class="login-page__left-panel">
            <div class="login-page__brand">PAPERWINGS</div>
            
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