@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Page Not Found
                </h1>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="modern-card modern-card--compact">
                <div class="modern-card__body text-center admin-error-page-content">
                    <div class="admin-error-icon-wrapper mb-4">
                        <div class="admin-error-icon-circle">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>

                    <h1 class="admin-error-code">404</h1>

                    <h2 class="admin-error-title mb-3">{{ $title ?? 'Page Not Found' }}</h2>

                    <p class="admin-error-message lead mb-4">
                        {{ $message ?? 'Oops! The page you\'re looking for seems to have flown away. It might have been moved, deleted, or the URL might be incorrect.' }}
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
                        <div class="admin-error-details mt-5 pt-4">
                            <details class="text-left">
                                <summary class="admin-error-details-summary">Error Details</summary>
                                <div class="admin-error-details-content mt-3 p-3">
                                    <pre>URL: {{ request()->fullUrl() }}
Method: {{ request()->method() }}
Route: {{ request()->route()->getName() ?? 'N/A' }}</pre>
                                </div>
                            </details>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

