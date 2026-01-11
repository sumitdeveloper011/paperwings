@php
    $hasCategories = false;
    if ($categories instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $hasCategories = $categories->total() > 0;
    } else {
        $hasCategories = $categories->count() > 0;
    }
@endphp

@if($hasCategories)
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
                        <span>Product Count</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Linked Products</span>
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
                @foreach($categories as $category)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <div class="category-image">
                                <img src="{{ $category->image_url }}"
                                     alt="{{ $category->name }}"
                                     class="category-image__img"
                                     onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div class="category-name">
                                <strong>{{ $category->name }}</strong>
                            </div>
                        </td>
                        <td class="modern-table__td" style="text-align: center;">
                            <div class="category-product-count">
                                <span class="badge badge--info">{{ $category->products_count ?? $category->products()->count() }}</span>
                            </div>
                        </td>
                        <td class="modern-table__td">
                            @php
                                $productCount = $category->products_count ?? $category->products()->count();
                            @endphp
                            @if($productCount > 0)
                                <a href="{{ route('admin.categories.products', $category) }}"
                                   class="btn-link btn-link--primary"
                                   title="View {{ $productCount }} product(s)">
                                    <i class="fas fa-link"></i>
                                    View Products ({{ $productCount }})
                                </a>
                            @else
                                <span class="text-muted">No products</span>
                            @endif
                        </td>
                        <td class="modern-table__td">
                            <form method="POST" action="{{ route('admin.categories.updateStatus', $category) }}" class="status-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select" data-category-id="{{ $category->id }}">
                                    <option value="1" {{ (int)$category->status === 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ (int)$category->status === 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                @can('categories.view')
                                <a href="{{ route('admin.categories.show', $category) }}"
                                   class="action-btn action-btn--view"
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('categories.edit')
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="action-btn action-btn--edit"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('categories.delete')
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
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        <div class="empty-state__icon">
            <i class="fas fa-folder-open"></i>
        </div>
        <h3 class="empty-state__title">No Categories Found</h3>
        @if(request()->get('search'))
            <p class="empty-state__text">No categories found matching "{{ request()->get('search') }}"</p>
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
