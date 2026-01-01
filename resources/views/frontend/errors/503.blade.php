@extends('layouts.frontend.main')

@section('content')
    <section class="error-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="error-content">
                        <div class="error-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h1 class="error-title">503</h1>
                        <h2 class="error-subtitle">Service Unavailable</h2>
                        <p class="error-description">
                            We're currently performing maintenance. We'll be back online shortly. Thank you for your patience.
                        </p>
                        <div class="error-actions">
                            <a href="javascript:location.reload();" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Refresh Page
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fas fa-home"></i> Go to Homepage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

