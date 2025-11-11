@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-tags"></i>
                    Categories
                </h1>
                <p class="page-header__subtitle">Manage and organize your product categories</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Category</span>
                </a>
                <a href="{{ route('admin.categories.getCategoriesForEposNow') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-download"></i>
                    <span>Get Categories from EposNow</span>
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
                    All Categories
                </h3>
                <p class="modern-card__subtitle">{{ $categories->total() }} total categories</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input" 
                               placeholder="Search categories..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.categories.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($categories->count() > 0)
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
                                    <span>Slug</span>
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
                            @foreach($categories as $category)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <div class="category-image">
                                            <img src="{{ $category->image_url }}" 
                                                 alt="{{ $category->name }}" 
                                                 class="category-image__img"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-name">
                                            <strong>{{ $category->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <code class="category-slug">{{ $category->slug }}</code>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.categories.updateStatus', $category) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="1" {{ $category->status === '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $category->status === '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $category->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.categories.show', $category) }}" 
                                               class="action-btn action-btn--view" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="action-btn action-btn--edit" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.categories.destroy', $category) }}" 
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this category?')">
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
                @if($categories->hasPages())
                    <div class="pagination-wrapper">
                        {{ $categories->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h3 class="empty-state__title">No Categories Found</h3>
                    @if($search)
                        <p class="empty-state__text">No categories found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Categories
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first category</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Category
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
