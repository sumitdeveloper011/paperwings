@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-info-circle"></i>
                    About Sections
                </h1>
                <p class="page-header__subtitle">Manage homepage about section</p>
            </div>
            <div class="page-header__actions">
                @can('about-sections.create')
                <a href="{{ route('admin.about-sections.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add About Section</span>
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
                    All About Sections
                </h3>
                <p class="modern-card__subtitle">{{ $aboutSections->total() }} total sections</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search sections..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.about-sections.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($aboutSections->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Image</th>
                                <th class="modern-table__th">Badge</th>
                                <th class="modern-table__th">Title</th>
                                <th class="modern-table__th">Description</th>
                                <th class="modern-table__th">Button</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th">Sort Order</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($aboutSections as $aboutSection)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        @if($aboutSection->image_url)
                                            <img src="{{ $aboutSection->image_url }}" alt="{{ $aboutSection->title }}"
                                                 class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        @if($aboutSection->badge)
                                            <span class="badge bg-secondary">{{ $aboutSection->badge }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <strong>{{ $aboutSection->title }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $aboutSection->description }}">
                                            {{ Str::limit($aboutSection->description, 40) }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        @if($aboutSection->button_text)
                                            <span class="badge bg-info">{{ $aboutSection->button_text }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.about-sections.updateStatus', $aboutSection) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="1" {{ $aboutSection->status ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$aboutSection->status ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">{{ $aboutSection->sort_order ?? 0 }}</td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            @can('about-sections.view')
                                            <a href="{{ route('admin.about-sections.show', $aboutSection) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('about-sections.edit')
                                            <a href="{{ route('admin.about-sections.edit', $aboutSection) }}"
                                               class="action-btn action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            @can('about-sections.delete')
                                            <form method="POST"
                                                  action="{{ route('admin.about-sections.destroy', $aboutSection) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this about section?')">
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

                <!-- Pagination -->
                @if($aboutSections->hasPages())
                    <div class="pagination-wrapper">
                        {{ $aboutSections->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="empty-state__title">No About Sections Found</h3>
                    @if($search)
                        <p class="empty-state__text">No sections found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.about-sections.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Sections
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first about section</p>
                        @can('about-sections.create')
                        <a href="{{ route('admin.about-sections.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add About Section
                        </a>
                        @endcan
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

