@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-box"></i>
                    Linked Products - {{ $category->name }}
                </h1>
                <p class="page-header__subtitle">Products linked to this category</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-icon">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Categories</span>
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
                    Products
                </h3>
                <p class="modern-card__subtitle">{{ $products->total() }} total products</p>
            </div>
        </div>

        <div class="modern-card__body">
            @if($products->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">
                                    <span>Image</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Name</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Price</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Status</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($products as $product)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <div class="category-image">
                                            <img src="{{ $product->main_image }}"
                                                 alt="{{ $product->name }}"
                                                 class="category-image__img"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-name">
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->short_description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($product->short_description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div>
                                            @if($product->discount_price)
                                                <span class="text-success"><strong>${{ number_format($product->discount_price, 2) }}</strong></span>
                                                <br>
                                                <small class="text-muted text-decoration-line-through">${{ number_format($product->total_price, 2) }}</small>
                                            @else
                                                <span><strong>${{ number_format($product->total_price, 2) }}</strong></span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge {{ $product->status === 1 ? 'badge--success' : 'badge--danger' }}">
                                            {{ $product->status === 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="pagination-wrapper">
                        {{ $products->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3 class="empty-state__title">No Products Found</h3>
                    <p class="empty-state__text">This category has no products linked to it.</p>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Categories
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
