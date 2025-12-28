@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-shipping-fast"></i>
                    Shipping Prices
                </h1>
                <p class="page-header__subtitle">Manage shipping prices by region</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.shipping-prices.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Shipping Price</span>
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
                    All Shipping Prices
                </h3>
                <p class="modern-card__subtitle">{{ $shippingPrices->total() }} total shipping prices</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search by region..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.shipping-prices.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($shippingPrices->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">
                                    <span>Sr. No.</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Region</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Shipping Price</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Free Shipping Minimum</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Status</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Created At</span>
                                </th>
                                <th class="modern-table__th modern-table__th--actions">
                                    <span>Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($shippingPrices as $shippingPrice)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <div class="sr-no">
                                            {{ ($shippingPrices->currentPage() - 1) * $shippingPrices->perPage() + $loop->iteration }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="region-name">
                                            <strong>{{ $shippingPrice->region->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="shipping-price">
                                            <strong>${{ number_format($shippingPrice->shipping_price, 2) }}</strong>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="free-shipping-minimum">
                                            @if($shippingPrice->free_shipping_minimum)
                                                <span class="badge bg-success">${{ number_format($shippingPrice->free_shipping_minimum, 2) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.shipping-prices.updateStatus', $shippingPrice) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                @php
                                                    $status = (int) $shippingPrice->status;
                                                @endphp
                                                <option value="1" {{ $status == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $status == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="date-info">
                                            <small>{{ $shippingPrice->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.shipping-prices.show', $shippingPrice) }}"
                                               class="action-btn action-btn--view"
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.shipping-prices.edit', $shippingPrice) }}"
                                               class="action-btn action-btn--edit"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.shipping-prices.destroy', $shippingPrice) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this shipping price?')">
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
                @if($shippingPrices->hasPages())
                    <div class="pagination-wrapper">
                        {{ $shippingPrices->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3 class="empty-state__title">No Shipping Prices Found</h3>
                    @if($search)
                        <p class="empty-state__text">No shipping prices found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.shipping-prices.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Shipping Prices
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first shipping price</p>
                        <a href="{{ route('admin.shipping-prices.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Shipping Price
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

