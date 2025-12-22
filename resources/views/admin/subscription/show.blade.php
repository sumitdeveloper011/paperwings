@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-envelope"></i>
                    Subscription Details
                </h1>
                <p class="page-header__subtitle">View subscription information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to List</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Subscription Information -->
            <div class="modern-card mb-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Subscription Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $subscription->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                {!! $subscription->status_badge !!}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Subscribed At:</span>
                            <span class="info-value">
                                @if($subscription->subscribed_at)
                                    {{ $subscription->subscribed_at->format('F d, Y \a\t h:i A') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Unsubscribed At:</span>
                            <span class="info-value">
                                @if($subscription->unsubscribed_at)
                                    {{ $subscription->unsubscribed_at->format('F d, Y \a\t h:i A') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Created At:</span>
                            <span class="info-value">
                                {{ $subscription->created_at->format('F d, Y \a\t h:i A') }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Updated At:</span>
                            <span class="info-value">
                                {{ $subscription->updated_at->format('F d, Y \a\t h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions -->
            <div class="modern-card mb-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-cog"></i>
                        Actions
                    </h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.subscriptions.updateStatus', $subscription) }}" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="1" {{ $subscription->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $subscription->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this subscription? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i>
                            Delete Subscription
                        </button>
                    </form>
                </div>
            </div>

            <!-- Unsubscribe Link -->
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-link"></i>
                        Unsubscribe Link
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="form-group">
                        <label class="form-label">Public Unsubscribe URL</label>
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ route('subscription.unsubscribe', $subscription->uuid) }}" 
                                   readonly
                                   id="unsubscribeLink">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyUnsubscribeLink()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Share this link to allow users to unsubscribe</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyUnsubscribeLink() {
    const linkInput = document.getElementById('unsubscribeLink');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        alert('Unsubscribe link copied to clipboard!');
    } catch (err) {
        console.error('Failed to copy:', err);
    }
}
</script>
@endsection

