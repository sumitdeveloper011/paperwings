@extends('layouts.admin.main')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-tools fa-5x text-secondary"></i>
                    </div>
                    <h1 class="display-1 font-weight-bold">503</h1>
                    <h2 class="mb-4">Service Unavailable</h2>
                    <p class="lead mb-4">
                        We're currently performing maintenance. We'll be back online shortly. Thank you for your patience.
                    </p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="javascript:location.reload();" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Refresh Page
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                    </div>
                    @if(app()->environment('local', 'development'))
                        <div class="mt-4">
                            <details class="text-left">
                                <summary class="cursor-pointer" style="cursor: pointer;">Error Details</summary>
                                <pre class="bg-light p-3 mt-2 rounded" style="background: #f8f9fa; padding: 1rem; margin-top: 0.5rem; border-radius: 0.25rem; overflow-x: auto;">{{ $exception->getMessage() ?? 'Service is temporarily unavailable due to maintenance.' }}</pre>
                            </details>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

