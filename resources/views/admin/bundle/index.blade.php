@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-boxes"></i>
                    Product Bundles
                </h1>
                <p class="page-header__subtitle">Manage product bundles</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Bundle</span>
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

    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">All Bundles</h3>
                <p class="modern-card__subtitle">{{ $bundles->total() }} total bundles</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search bundles..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.bundles.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($bundles->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Image</th>
                                <th class="modern-table__th">Name</th>
                                <th class="modern-table__th">Products</th>
                                <th class="modern-table__th">Bundle Price</th>
                                <th class="modern-table__th">Discount</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($bundles as $bundle)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        @if($bundle->image)
                                            <img src="{{ asset('storage/' . $bundle->image) }}"
                                                 alt="{{ $bundle->name }}"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                                 onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                        @else
                                            <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <strong>{{ $bundle->name }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge bg-primary">{{ $bundle->products_count }}</span>
                                    </td>
                                    <td class="modern-table__td">
                                        <strong>${{ number_format($bundle->bundle_price, 2) }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        @if($bundle->discount_percentage)
                                            <span class="badge bg-success">{{ $bundle->discount_percentage }}%</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.bundles.updateStatus', $bundle) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="1" {{ $bundle->status ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$bundle->status ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.bundles.show', $bundle) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.bundles.edit', $bundle) }}"
                                               class="action-btn action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.bundles.destroy', $bundle) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this bundle?')">
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

                @if($bundles->hasPages())
                    <div class="pagination-wrapper">
                        {{ $bundles->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="empty-state__title">No Bundles Found</h3>
                    <p class="empty-state__text">Start by creating your first bundle</p>
                    <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Bundle
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

