@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-question"></i>
                    Product FAQs
                </h1>
                <p class="page-header__subtitle">Manage frequently asked questions for products</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.product-faqs.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add FAQ</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Product FAQs
                </h3>
                <p class="modern-card__subtitle">{{ $faqs->total() }} total FAQs</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="filter-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search FAQs..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.product-faqs.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                    @if(isset($categories))
                        <select name="category_id" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ isset($categoryId) && $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @if(isset($products))
                        <select name="product_id" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ isset($productId) && $productId == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($faqs->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Product</th>
                                <th class="modern-table__th">Category</th>
                                <th class="modern-table__th">FAQs Count</th>
                                <th class="modern-table__th">Created</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($faqs as $faq)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <strong>{{ $faq->product->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        {{ $faq->category->name ?? 'N/A' }}
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge bg-primary">{{ count($faq->faqs ?? []) }} FAQ(s)</span>
                                    </td>
                                    <td class="modern-table__td">{{ $faq->created_at->format('M d, Y') }}</td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.product-faqs.show', $faq) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.product-faqs.edit', $faq) }}"
                                               class="action-btn action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.product-faqs.destroy', $faq) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this FAQ?')">
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

                @if($faqs->hasPages())
                    <div class="pagination-wrapper">
                        {{ $faqs->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3 class="empty-state__title">No FAQs Found</h3>
                    <p class="empty-state__text">Start by creating your first product FAQ</p>
                    <a href="{{ route('admin.product-faqs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add FAQ
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

