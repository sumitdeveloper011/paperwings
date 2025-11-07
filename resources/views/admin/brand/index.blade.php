@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-award"></i>
                    Brands
                </h1>
                <p class="page-header__subtitle">Manage and organize your product brands</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Brand</span>
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
                    All Brands
                </h3>
                <p class="modern-card__subtitle">{{ $brands->total() }} total brands</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input" 
                               placeholder="Search brands..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.brands.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($brands->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">
                                    <span>Logo</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Name</span>
                                </th>
                                <th class="modern-table__th">
                                    <span>Slug</span>
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
                            @foreach($brands as $brand)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <div class="category-image">
                                            <img src="{{ $brand->image_url }}" 
                                                 alt="{{ $brand->name }}" 
                                                 class="category-image__img"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-name">
                                            <strong>{{ $brand->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <code class="category-slug">{{ $brand->slug }}</code>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $brand->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.brands.show', $brand) }}" 
                                               class="action-btn action-btn--view" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.brands.edit', $brand) }}" 
                                               class="action-btn action-btn--edit" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.brands.destroy', $brand) }}" 
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this brand?')">
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
                @if($brands->hasPages())
                    <div class="pagination-wrapper">
                        {{ $brands->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="empty-state__title">No Brands Found</h3>
                    @if($search)
                        <p class="empty-state__text">No brands found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Brands
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first brand</p>
                        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Brand
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
