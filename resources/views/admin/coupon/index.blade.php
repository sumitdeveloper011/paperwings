@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-ticket-alt"></i>
                    Coupons
                </h1>
                <p class="page-header__subtitle">Manage discount coupons and promotional codes</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Coupon</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Coupons
                </h3>
                <p class="modern-card__subtitle">{{ $coupons->total() }} total coupons</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search coupons..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.coupons.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($coupons->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">
                                    <span>Sr. No.</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Code</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Name</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Type</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Value</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Usage</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Validity</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Status</span>
                                </th>
                                <th class="modern-table__th modern-table__th--actions">
                                    <span>Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($coupons as $coupon)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <div class="sr-no">
                                            {{ ($coupons->currentPage() - 1) * $coupons->perPage() + $loop->iteration }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="coupon-code">
                                            <code class="code-badge">{{ $coupon->code }}</code>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="coupon-name">
                                            <strong>{{ $coupon->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge {{ $coupon->type === 'percentage' ? 'bg-info' : 'bg-primary' }}">
                                            {{ ucfirst($coupon->type) }}
                                        </span>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="coupon-value">
                                            @if($coupon->type === 'percentage')
                                                {{ $coupon->value }}%
                                            @else
                                                ${{ number_format($coupon->value, 2) }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="coupon-usage">
                                            {{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="coupon-dates">
                                            <small>{{ $coupon->start_date->format('M d, Y') }}</small>
                                            <br>
                                            <small>to {{ $coupon->end_date->format('M d, Y') }}</small>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.coupons.updateStatus', $coupon) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                @php
                                                    $status = (int) $coupon->status;
                                                @endphp
                                                <option value="1" {{ $status == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $status == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.coupons.show', $coupon) }}"
                                               class="action-btn action-btn--view"
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                               class="action-btn action-btn--edit"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.coupons.destroy', $coupon) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this coupon?')">
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

                <!-- Pagination -->
                @if($coupons->hasPages())
                    <div class="pagination-wrapper">
                        {{ $coupons->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h3 class="empty-state__title">No Coupons Found</h3>
                    @if($search)
                        <p class="empty-state__text">No coupons found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Coupons
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first coupon</p>
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Coupon
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

