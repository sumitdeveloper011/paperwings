@extends('layouts.frontend.main')

@section('content')
    <section class="error-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="error-content">
                        <div class="error-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h1 class="error-title">429</h1>
                        <h2 class="error-subtitle">Too Many Requests</h2>
                        <p class="error-description">
                            You've made too many requests in a short period. Please wait a moment before trying again.
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
                            <p>If you continue to see this message, please wait a few minutes before trying again.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
