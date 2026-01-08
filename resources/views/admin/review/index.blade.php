@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-star"></i>
                    Product Reviews
                </h1>
                <p class="page-header__subtitle">Manage customer product reviews</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Reviews
                </h3>
                <p class="modern-card__subtitle">{{ $reviews->total() }} total reviews</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="filter-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search reviews..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.reviews.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                    <select name="status" class="filter-select">
                        <option value="">All Status</option>
                        <option value="0" {{ $status === '0' ? 'selected' : '' }}>Pending</option>
                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Approved</option>
                        <option value="2" {{ $status === '2' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($reviews->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Product</th>
                                <th class="modern-table__th">Reviewer</th>
                                <th class="modern-table__th">Rating</th>
                                <th class="modern-table__th">Review</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th">Date</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($reviews as $review)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <strong>{{ $review->product->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        <div>
                                            <strong>{{ $review->reviewer_name ?? $review->name }}</strong>
                                            @if($review->verified_purchase)
                                                <span class="badge bg-success ms-1" title="Verified Purchase">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            @endif
                                        </div>
                                        @if($review->email)
                                            <small class="text-muted">{{ $review->email }}</small>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="rating-display">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="ms-1">({{ $review->rating }}/5)</span>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $review->review }}">
                                            {{ Str::limit($review->review, 60) }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.reviews.updateStatus', $review) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="0" {{ $review->status == 0 ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ $review->status == 1 ? 'selected' : '' }}>Approved</option>
                                                <option value="2" {{ $review->status == 2 ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        {{ $review->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.reviews.show', $review) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.reviews.destroy', $review) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this review?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn action-btn--delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($reviews->hasPages())
                    <div class="pagination-wrapper">
                        {{ $reviews->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="empty-state__title">No Reviews Found</h3>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
