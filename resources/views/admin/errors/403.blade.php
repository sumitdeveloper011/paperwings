@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-ban"></i>
                    Access Denied
                </h1>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__body text-center error-page-content">
                    <div class="error-icon-wrapper mb-4">
                        <div class="error-icon-circle">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>

                    <h1 class="error-code">403</h1>

                    <h2 class="error-title mb-3">Access Forbidden</h2>

                    <p class="error-message lead mb-4">
                        @php
                            $errorMessage = $exception->getMessage() ?? 'You do not have permission to access this resource.';
                            // Clean up common error messages
                            if (str_contains($errorMessage, 'does not have the right roles')) {
                                $errorMessage = 'You do not have the required role to access this resource.';
                            } elseif (str_contains($errorMessage, 'does not have the right permissions')) {
                                $errorMessage = 'You do not have the required permissions to access this resource.';
                            }
                        @endphp
                        {{ $errorMessage }}
                    </p>

                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-icon">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Go to Dashboard</span>
                        </a>
                        <a href="javascript:history.back();" class="btn btn-outline-secondary btn-icon">
                            <i class="fas fa-arrow-left"></i>
                            <span>Go Back</span>
                        </a>
                    </div>

                    @if(app()->environment('local', 'development'))
                        <div class="error-details mt-5 pt-4">
                            <details class="text-left">
                                <summary class="error-details-summary">Error Details</summary>
                                <div class="error-details-content mt-3 p-3">
                                    <pre>Access Denied: {{ $exception->getMessage() ?? 'You do not have permission to access this resource.' }}</pre>
                                </div>
                            </details>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.error-page-content {
    padding: 4rem 2rem;
}

.error-icon-wrapper {
    display: flex;
    justify-content: center;
}

.error-icon-circle {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    background: linear-gradient(135deg, var(--danger-color) 0%, #ee5a6f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(233, 92, 103, 0.3);
}

.error-icon-circle i {
    font-size: 4rem;
    color: white;
}

.error-code {
    font-size: 5rem;
    font-weight: bold;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.error-title {
    color: var(--text-primary);
    font-size: 1.75rem;
    margin-bottom: 1rem;
}

.error-message {
    color: var(--text-secondary);
    font-size: 1.1rem;
    margin-bottom: 2rem;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.error-details {
    border-top: 1px solid var(--border-color);
    margin-top: 3rem;
    padding-top: 1.5rem;
}

.error-details-summary {
    cursor: pointer;
    color: var(--text-secondary);
    font-weight: 500;
}

.error-details-content {
    background: #f8f9fa;
    padding: 1rem;
    margin-top: 0.75rem;
    border-radius: 0.5rem;
    overflow-x: auto;
}

.error-details-content pre {
    margin: 0;
    color: #495057;
    font-size: 0.875rem;
}
</style>
@endpush
@endsection
