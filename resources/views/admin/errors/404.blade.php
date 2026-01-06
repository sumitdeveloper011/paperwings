@extends('layouts.admin.main')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-5x text-info"></i>
                    </div>
                    <h1 class="display-1 font-weight-bold">404</h1>
                    <h2 class="mb-4">{{ $title ?? 'Page Not Found' }}</h2>
                    <p class="lead mb-4">
                        {{ $message ?? 'Oops! The page you\'re looking for seems to have flown away. It might have been moved, deleted, or the URL might be incorrect.' }}
                    </p>
                    <div class="d-flex gap-2 justify-content-center flex-wrap">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                        </a>
                        <a href="javascript:history.back();" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                    </div>
                    @if(app()->environment('local', 'development'))
                        <div class="mt-4">
                            <details class="text-left">
                                <summary class="cursor-pointer" style="cursor: pointer;">Error Details</summary>
                                <pre class="bg-light p-3 mt-2 rounded" style="background: #f8f9fa; padding: 1rem; margin-top: 0.5rem; border-radius: 0.25rem; overflow-x: auto;">URL: {{ request()->fullUrl() }}<br>Method: {{ request()->method() }}</pre>
                            </details>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

