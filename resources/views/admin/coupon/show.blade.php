@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-ticket-alt"></i>
                    Coupon Details
                </h1>
                <p class="page-header__subtitle">View coupon information</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-edit"></i>
                    <span>Edit Coupon</span>
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Coupons</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Coupon Information
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-ticket-alt"></i>
                                Coupon Code
                            </div>
                            <div class="info-item__value">
                                <code class="code-badge code-badge--large">{{ $coupon->code }}</code>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-tag"></i>
                                Coupon Name
                            </div>
                            <div class="info-item__value">
                                <strong>{{ $coupon->name }}</strong>
                            </div>
                        </div>

                        @if($coupon->description)
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-align-left"></i>
                                Description
                            </div>
                            <div class="info-item__value">
                                {{ $coupon->description }}
                            </div>
                        </div>
                        @endif

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-percent"></i>
                                Discount Type
                            </div>
                            <div class="info-item__value">
                                <span class="badge {{ $coupon->type === 'percentage' ? 'bg-info' : 'bg-primary' }}">
                                    {{ ucfirst($coupon->type) }}
                                </span>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-dollar-sign"></i>
                                Discount Value
                            </div>
                            <div class="info-item__value">
                                @if($coupon->type === 'percentage')
                                    <strong>{{ $coupon->value }}%</strong>
                                @else
                                    <strong>${{ number_format($coupon->value, 2) }}</strong>
                                @endif
                            </div>
                        </div>

                        @if($coupon->minimum_amount)
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-shopping-cart"></i>
                                Minimum Amount
                            </div>
                            <div class="info-item__value">
                                ${{ number_format($coupon->minimum_amount, 2) }}
                            </div>
                        </div>
                        @endif

                        @if($coupon->maximum_discount)
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-tag"></i>
                                Maximum Discount
                            </div>
                            <div class="info-item__value">
                                ${{ number_format($coupon->maximum_discount, 2) }}
                            </div>
                        </div>
                        @endif

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-users"></i>
                                Usage Limit
                            </div>
                            <div class="info-item__value">
                                {{ $coupon->usage_limit ?? 'Unlimited' }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-chart-line"></i>
                                Usage Count
                            </div>
                            <div class="info-item__value">
                                <strong>{{ $coupon->usage_count }}</strong>
                            </div>
                        </div>

                        @if($coupon->usage_limit_per_user)
                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-user"></i>
                                Usage Limit Per User
                            </div>
                            <div class="info-item__value">
                                {{ $coupon->usage_limit_per_user }}
                            </div>
                        </div>
                        @endif

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-calendar-alt"></i>
                                Start Date
                            </div>
                            <div class="info-item__value">
                                {{ $coupon->start_date->format('F d, Y') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-calendar-check"></i>
                                End Date
                            </div>
                            <div class="info-item__value">
                                {{ $coupon->end_date->format('F d, Y') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </div>
                            <div class="info-item__value">
                                {!! $coupon->status_badge !!}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-clock"></i>
                                Created At
                            </div>
                            <div class="info-item__value">
                                {{ $coupon->created_at->format('F d, Y h:i A') }}
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-item__label">
                                <i class="fas fa-edit"></i>
                                Updated At
                            </div>
                            <div class="info-item__value">
                                {{ $coupon->updated_at->format('F d, Y h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-cog"></i>
                        Actions
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="action-list">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="action-item">
                            <i class="fas fa-edit"></i>
                            <span>Edit Coupon</span>
                        </a>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-item action-item--danger">
                                <i class="fas fa-trash"></i>
                                <span>Delete Coupon</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modern-card">
                <div class="modern-card__header">
                    <h3 class="modern-card__title">
                        <i class="fas fa-info-circle"></i>
                        Coupon Status
                    </h3>
                </div>
                <div class="modern-card__body">
                    <div class="status-info">
                        @if($coupon->isValid())
                            <div class="status-badge status-badge--success">
                                <i class="fas fa-check-circle"></i>
                                Valid & Active
                            </div>
                        @elseif($coupon->isExpired())
                            <div class="status-badge status-badge--warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Expired
                            </div>
                        @elseif(!$coupon->isActive())
                            <div class="status-badge status-badge--danger">
                                <i class="fas fa-times-circle"></i>
                                Inactive
                            </div>
                        @else
                            <div class="status-badge status-badge--info">
                                <i class="fas fa-info-circle"></i>
                                Usage Limit Reached
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

