@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-bullhorn"></i>
                    Special Offers Banners
                </h1>
            </div>
            <div class="page-header__actions">
                @can('special-offers.create')
                <a href="{{ route('admin.special-offers-banners.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Banner</span>
                </a>
                @endcan
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
    <div class="modern-card modern-card--compact">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Banners
                </h3>
                <p class="modern-card__subtitle">{{ $banners->total() }} total banners</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search banners..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.special-offers-banners.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($banners->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Image</th>
                                <th class="modern-table__th">Title</th>
                                <th class="modern-table__th">Description</th>
                                <th class="modern-table__th">Button</th>
                                <th class="modern-table__th">Date Range</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th">Sort Order</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($banners as $banner)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        @if($banner->image_url)
                                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                                 class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <strong>{{ $banner->title }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $banner->description }}">
                                            {{ Str::limit($banner->description, 40) }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        @if($banner->button_text)
                                            <span class="badge bg-info">{{ $banner->button_text }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <small>
                                            @if($banner->start_date)
                                                {{ $banner->start_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">No start</span>
                                            @endif
                                            <br>
                                            @if($banner->end_date)
                                                {{ $banner->end_date->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">No end</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.special-offers-banners.updateStatus', $banner) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="1" {{ $banner->status ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$banner->status ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">{{ $banner->sort_order ?? 0 }}</td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            @can('special-offers.view')
                                            <a href="{{ route('admin.special-offers-banners.show', $banner) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('special-offers.edit')
                                            <a href="{{ route('admin.special-offers-banners.edit', $banner) }}"
                                               class="action-btn action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('special-offers.delete')
                                            <form method="POST"
                                                  action="{{ route('admin.special-offers-banners.destroy', $banner) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this banner?')">
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

                @if($banners->hasPages())
                    <div class="pagination-wrapper">
                        {{ $banners->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3 class="empty-state__title">No Banners Found</h3>
                    <p class="empty-state__text">Start by creating your first special offers banner</p>
                    @can('special-offers.create')
                    <a href="{{ route('admin.special-offers-banners.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Banner
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

