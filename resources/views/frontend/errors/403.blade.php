@extends('layouts.frontend.main')

@section('content')
    <section class="error-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="error-content">
                        <div class="error-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <h1 class="error-title">403</h1>
                        <h2 class="error-subtitle">Access Forbidden</h2>
                        <p class="error-description">
                            You don't have permission to access this resource. This page is restricted or you may need to login with the correct account.
                        </p>
                        <div class="error-actions">
                            @auth
                                <a href="{{ route('home') }}" class="btn btn-primary">
                                    <i class="fas fa-home"></i> Go to Homepage
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-home"></i> Go to Homepage
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

