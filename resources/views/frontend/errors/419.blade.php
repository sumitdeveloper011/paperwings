@extends('layouts.frontend.main')

@section('content')
    <section class="error-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="error-content">
                        <div class="error-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h1 class="error-title">419</h1>
                        <h2 class="error-subtitle">Page Expired</h2>
                        <p class="error-description">
                            Your session has expired for security reasons. This usually happens when a form page has been open for too long. Please refresh the page and try again.
                        </p>
                        <div class="error-actions">
                            <a href="javascript:location.reload();" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Refresh Page
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fas fa-home"></i> Go to Homepage
                            </a>
                        </div>
                        <div class="error-help">
                            <p><strong>What happened?</strong></p>
                            <ul class="error-help-list">
                                <li>Your CSRF token has expired</li>
                                <li>The form was open for too long</li>
                                <li>Your browser session timed out</li>
                            </ul>
                            <p><strong>Solution:</strong> Simply refresh the page and resubmit your form.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

