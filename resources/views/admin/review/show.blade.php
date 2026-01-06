@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-star"></i>
                    Review Details
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Review Information</h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-row">
                        <strong>Product:</strong>
                        <span>
                            <a href="{{ route('admin.products.show', $review->product) }}" target="_blank">
                                {{ $review->product->name ?? 'N/A' }}
                            </a>
                        </span>
                    </div>
                    <div class="info-row">
                        <strong>Reviewer:</strong>
                        <span>{{ $review->reviewer_name ?? $review->name }}</span>
                        @if($review->verified_purchase)
                            <span class="badge bg-success ms-2" title="Verified Purchase">
                                <i class="fas fa-check-circle"></i> Verified Purchase
                            </span>
                        @endif
                    </div>
                    <div class="info-row">
                        <strong>Email:</strong>
                        <span>{{ $review->email }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Rating:</strong>
                        <div class="rating-display">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 1.2rem;"></i>
                            @endfor
                            <span class="ms-2">({{ $review->rating }}/5)</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <strong>Review:</strong>
                        <div class="mt-2 p-3" style="background: #f8f9fa; border-radius: 5px;">
                            {{ $review->review }}
                        </div>
                    </div>
                    <div class="info-row">
                        <strong>Status:</strong>
                        <form method="POST" action="{{ route('admin.reviews.updateStatus', $review) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <option value="0" {{ $review->status == 0 ? 'selected' : '' }}>Pending</option>
                                <option value="1" {{ $review->status == 1 ? 'selected' : '' }}>Approved</option>
                                <option value="2" {{ $review->status == 2 ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </form>
                    </div>
                    @if($review->helpful_count > 0)
                    <div class="info-row">
                        <strong>Helpful Count:</strong>
                        <span>{{ $review->helpful_count }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <strong>Created:</strong>
                        <span>{{ $review->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <strong>Updated:</strong>
                        <span>{{ $review->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            <div class="modern-card mt-4">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">Actions</h3>
                </div>
                <div class="modern-card__body">
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                          onsubmit="return confirm('Are you sure you want to delete this review? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Review
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-display {
    display: flex;
    align-items: center;
}
.info-row {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}
.info-row:last-child {
    border-bottom: none;
}
.info-row strong {
    display: inline-block;
    min-width: 150px;
    color: #495057;
}
</style>
@endsection

