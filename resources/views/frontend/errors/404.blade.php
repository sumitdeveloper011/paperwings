@extends('layouts.frontend.main')

@section('content')
    <section class="error-404-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="error-404__content">
                        <div class="error-404__icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h1 class="error-404__title">404</h1>
                        <h2 class="error-404__subtitle">{{ $title ?? 'Page Not Found' }}</h2>
                        <p class="error-404__description">
                            {{ $message ?? 'Oops! The page you\'re looking for seems to have flown away. It might have been moved, deleted, or the URL might be incorrect.' }}
                        </p>
                        <div class="error-404__actions">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-home"></i> Go to Homepage
                            </a>
                            <a href="{{ route('product.by.category', 'all') }}" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-bag"></i> Browse Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

