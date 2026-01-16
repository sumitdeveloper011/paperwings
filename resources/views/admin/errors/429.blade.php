@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="error-page">
        <div class="error-page__content">
            <div class="error-page__icon">
                <i class="fas fa-clock"></i>
            </div>
            <h1 class="error-page__title">429</h1>
            <h2 class="error-page__subtitle">Too Many Requests</h2>
            <p class="error-page__description">
                You've made too many requests in a short period. Please wait a moment before trying again.
            </p>
            <div class="error-page__actions">
                <a href="javascript:location.reload();" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Try Again
                </a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
