@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-layer-group"></i>
                    Sub Categories
                </h1>
                <p class="page-header__subtitle">Manage and organize your product subcategories</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Sub Category</span>
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
                    All Sub Categories
                </h3>
                <p class="modern-card__subtitle">{{ $subCategories->total() }} total subcategories</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <select name="category_id" class="form-select-modern" onchange="this.form.submit()" style="width: 200px; margin-right: 0.5rem;">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input" 
                               placeholder="Search subcategories..." value="{{ $search }}">
                        @if($search || $categoryId)
                            <a href="{{ route('admin.subcategories.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($subCategories->count() > 0)
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
                            @foreach($subCategories as $subCategory)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <div class="category-image">
                                            <img src="{{ $subCategory->image_url }}" 
                                                 alt="{{ $subCategory->name }}" 
                                                 class="category-image__img"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-name">
                                            <strong>{{ $subCategory->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge badge--info">
                                            <i class="fas fa-tag"></i>
                                            {{ $subCategory->category->name }}
                                        </span>
                                    </td>
                                    <td class="modern-table__td">
                                        <code class="category-slug">{{ $subCategory->slug }}</code>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.subcategories.updateStatus', $subCategory) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" data-subcategory-id="{{ $subCategory->id }}">
                                                <option value="1" {{ $subCategory->status === '1' ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $subCategory->status === '0' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="category-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $subCategory->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.subcategories.show', $subCategory) }}" 
                                               class="action-btn action-btn--view" 
                                               title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.subcategories.edit', $subCategory) }}" 
                                               class="action-btn action-btn--edit" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.subcategories.destroy', $subCategory) }}" 
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this subcategory?')">
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
                @if($subCategories->hasPages())
                    <div class="pagination-wrapper">
                        {{ $subCategories->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h3 class="empty-state__title">No Sub Categories Found</h3>
                    @if($search)
                        <p class="empty-state__text">No subcategories found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Sub Categories
                        </a>
                    @elseif($categoryId)
                        <p class="empty-state__text">No subcategories found in selected category</p>
                        <a href="{{ route('admin.subcategories.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Sub Categories
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first subcategory</p>
                        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Sub Category
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('status-select')) {
            e.preventDefault();
            e.stopPropagation();

            const select = e.target;
            const form = select.closest('.status-form');
            if (!form) return;

            const subcategoryId = select.getAttribute('data-subcategory-id');
            const newStatus = select.value;
            const originalValue = select.value === '1' ? '0' : '1';

            select.disabled = true;
            const originalText = select.options[select.selectedIndex].textContent;
            select.options[select.selectedIndex].textContent = 'Updating...';

            const csrfToken = form.querySelector('input[name="_token"]').value;
            const formAction = form.getAttribute('action');

            fetch(formAction, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({
                    '_token': csrfToken,
                    '_method': 'PATCH',
                    'status': newStatus
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                select.disabled = false;
                select.options[select.selectedIndex].textContent = originalText;

                if (data && data.message) {
                    if (typeof showToast === 'function') {
                        showToast('Success', data.message, 'success', 3000);
                    }
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                select.value = originalValue;
                select.disabled = false;
                select.options[select.selectedIndex].textContent = originalText;
                if (typeof showToast === 'function') {
                    showToast('Error', 'Failed to update subcategory status', 'error', 5000);
                } else {
                    alert('Error updating status. Please try again.');
                }
            });
        }
    });
});
</script>
@endpush
@endsection
