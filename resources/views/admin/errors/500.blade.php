@php
    $isAuthenticated = auth()->check();
    $layout = $isAuthenticated ? 'layouts.admin.main' : 'layouts.admin.auth';
@endphp

@extends($layout)

@section('content')
<div class="admin-content" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="modern-card">
                    <div class="modern-card__body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle fa-5x text-danger"></i>
                        </div>
                        <h1 class="display-1 font-weight-bold mb-3">500</h1>
                        <h2 class="mb-4">Internal Server Error</h2>
                        <p class="lead mb-4">
                            Something went wrong on our end. We're working to fix the issue. Please try again in a few moments.
                        </p>
                        <div class="d-flex gap-2 justify-content-center flex-wrap mb-4">
                            <a href="javascript:location.reload();" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Try Again
                            </a>
                            @if($isAuthenticated)
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('admin.login') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-sign-in-alt"></i> Go to Login
                                </a>
                            @endif
                        </div>
                        @if(app()->environment('local', 'development') && isset($exception))
                            <div class="mt-4">
                                <details class="text-left">
                                    <summary class="cursor-pointer" style="cursor: pointer;">Error Details</summary>
                                    <pre class="bg-light p-3 mt-2 rounded" style="background: #f8f9fa; padding: 1rem; margin-top: 0.5rem; border-radius: 0.25rem; overflow-x: auto;">{{ $exception->getMessage() ?? 'Unknown error' }}</pre>
                                </details>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

