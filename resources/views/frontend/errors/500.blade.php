@extends('layouts.frontend.main')

@section('content')
    <section class="error-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="error-content">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <h1 class="error-title">500</h1>
                        <h2 class="error-subtitle">Internal Server Error</h2>
                        <p class="error-description">
                            Something went wrong on our end. We're working to fix the issue. Please try again in a few moments.
                        </p>
                        <div class="error-actions">
                            <a href="javascript:location.reload();" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Try Again
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fas fa-home"></i> Go to Homepage
                            </a>
                        </div>
                        <div class="error-help">
                            <p>If the problem persists, please contact our support team.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

