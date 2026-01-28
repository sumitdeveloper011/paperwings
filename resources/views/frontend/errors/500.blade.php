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
                            @if(app()->environment('local', 'development') || config('app.debug'))
                                An error occurred. See details below.
                            @else
                                Something went wrong on our end. We're working to fix the issue. Please try again in a few moments.
                            @endif
                        </p>
                        <div class="error-actions">
                            <a href="javascript:location.reload();" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Try Again
                            </a>
                            @php
                                $homeUrl = null;
                                try {
                                    $homeUrl = route('home');
                                } catch (\Exception $e) {
                                    $homeUrl = url('/');
                                }
                            @endphp
                            <a href="{{ $homeUrl }}" class="btn btn-outline-primary">
                                <i class="fas fa-home"></i> Go to Homepage
                            </a>
                        </div>
                        
                        @if(app()->environment('local', 'development') || config('app.debug'))
                            <div class="error-details mt-4" style="text-align: left; margin-top: 2rem;">
                                @if(isset($exception))
                                    <details open style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 1rem;">
                                        <summary style="cursor: pointer; font-weight: bold; margin-bottom: 1rem; color: #dc3545;">
                                            <i class="fas fa-bug"></i> Error Details (Development Mode)
                                        </summary>
                                        <div style="background: #fff; padding: 1rem; border-radius: 0.25rem; margin-top: 0.5rem;">
                                            <div style="margin-bottom: 1rem;">
                                                <strong>Error Message:</strong>
                                                <pre style="background: #f8f9fa; padding: 0.75rem; border-radius: 0.25rem; overflow-x: auto; margin-top: 0.5rem; white-space: pre-wrap; word-wrap: break-word;">{{ $exception->getMessage() ?? 'Unknown error' }}</pre>
                                            </div>
                                            <div style="margin-bottom: 1rem;">
                                                <strong>File:</strong>
                                                <code style="background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 0.25rem; display: block; margin-top: 0.5rem;">{{ $exception->getFile() ?? 'Unknown' }}</code>
                                            </div>
                                            <div style="margin-bottom: 1rem;">
                                                <strong>Line:</strong>
                                                <code style="background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">{{ $exception->getLine() ?? 'Unknown' }}</code>
                                            </div>
                                            @if($exception->getTraceAsString())
                                            <div>
                                                <strong>Stack Trace:</strong>
                                                <details style="margin-top: 0.5rem;">
                                                    <summary style="cursor: pointer; color: #6c757d;">Click to view stack trace</summary>
                                                    <pre style="background: #f8f9fa; padding: 0.75rem; border-radius: 0.25rem; overflow-x: auto; margin-top: 0.5rem; max-height: 400px; overflow-y: auto; font-size: 0.85rem; white-space: pre-wrap; word-wrap: break-word;">{{ $exception->getTraceAsString() }}</pre>
                                                </details>
                                            </div>
                                            @endif
                                        </div>
                                    </details>
                                @elseif(isset($error))
                                    <details open style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.25rem; padding: 1rem;">
                                        <summary style="cursor: pointer; font-weight: bold; margin-bottom: 1rem; color: #dc3545;">
                                            <i class="fas fa-bug"></i> Error Details (Development Mode)
                                        </summary>
                                        <pre style="background: #fff; padding: 1rem; border-radius: 0.25rem; margin-top: 0.5rem; overflow-x: auto; white-space: pre-wrap; word-wrap: break-word;">{{ $error }}</pre>
                                    </details>
                                @else
                                    <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 0.25rem; padding: 1rem; color: #856404;">
                                        <i class="fas fa-info-circle"></i> No error details available. Check Laravel logs: <code>storage/logs/laravel.log</code>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="error-help">
                                <p>If the problem persists, please contact our support team.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

