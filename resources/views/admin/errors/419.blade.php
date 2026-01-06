@extends('layouts.admin.main')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-shield-alt fa-5x text-danger"></i>
                    </div>
                    <h1 class="display-1 font-weight-bold">419</h1>
                    <h2 class="mb-4">Page Expired</h2>
                    <p class="lead mb-4">
                        Your session has expired for security reasons. This usually happens when a form page has been open for too long. Please refresh the page and try again.
                    </p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="javascript:location.reload();" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Refresh Page
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                    </div>
                    <div class="mt-4 text-left">
                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle"></i> What happened?</strong>
                            <ul class="mb-0 mt-2">
                                <li>Your CSRF token has expired</li>
                                <li>The form was open for too long</li>
                                <li>Your browser session timed out</li>
                            </ul>
                        </div>
                        <p class="text-muted"><strong>Solution:</strong> Simply refresh the page and resubmit your form.</p>
                    </div>
                    @if(app()->environment('local', 'development'))
                        <div class="mt-4">
                            <details class="text-left">
                                <summary class="cursor-pointer" style="cursor: pointer;">Error Details</summary>
                                <pre class="bg-light p-3 mt-2 rounded" style="background: #f8f9fa; padding: 1rem; margin-top: 0.5rem; border-radius: 0.25rem; overflow-x: auto;">CSRF Token Mismatch: {{ $exception->getMessage() ?? 'Your session has expired. Please refresh the page and try again.' }}</pre>
                            </details>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

