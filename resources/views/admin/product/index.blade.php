@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-box"></i>
                    Products
                </h1>
                <p class="page-header__subtitle">Manage and organize your products</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </a>
                <a href="{{ route('admin.products.getProductsForEposNow') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-download"></i>
                    <span>Get Products from EposNow</span>
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
                    All Products
                </h3>
                <p class="modern-card__subtitle">{{ $products->total() }} total products</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper" style="flex-wrap: wrap; gap: 0.5rem;">
                        <select name="category_id" class="form-select-modern" onchange="this.form.submit()" style="width: 180px;">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="subcategory_id" class="form-select-modern" onchange="this.form.submit()" style="width: 180px;">
                            <option value="">All Sub Categories</option>
                            @foreach($subCategories as $subCategory)
                                <option value="{{ $subCategory->id }}" {{ $subCategoryId == $subCategory->id ? 'selected' : '' }}>
                                    {{ $subCategory->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="brand_id" class="form-select-modern" onchange="this.form.submit()" style="width: 150px;">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input" 
                               placeholder="Search products..." value="{{ $search }}" style="width: 200px;">
                        @if($search || $categoryId || $subCategoryId || $brandId)
                            <a href="{{ route('admin.products.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
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
                                    <span>Category</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Brand</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Price</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Status</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Created</span>
                                </th>
                                <th class="modern-table__th modern-table__th--actions">
                                    <span>Actions</span>
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
                                                 onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
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
                                            <span class="badge badge--info">
                                                <i class="fas fa-tag"></i>
                                                {{ $product->category->name }}
                                            </span>
                                            @if($product->subCategory)
                                                <br>
                                                <small class="text-muted">{{ $product->subCategory->name }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        @if($product->brand)
                                            <span class="badge badge--secondary">
                                                {{ $product->brand->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <div>
                                            <strong class="text-success">${{ number_format($product->total_price, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">Ex. Tax: ${{ number_format($product->price_without_tax, 2) }}</small>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.products.updateStatus', $product) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $product->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.products.show', $product) }}" 
                                               class="action-btn action-btn--view" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}" 
                                               class="action-btn action-btn--edit" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.products.destroy', $product) }}" 
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this product?')">
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
                    @if($search)
                        <p class="empty-state__text">No products found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Products
                        </a>
                    @elseif($categoryId || $subCategoryId || $brandId)
                        <p class="empty-state__text">No products found with selected filters</p>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Products
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first product</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Product
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
