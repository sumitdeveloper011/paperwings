@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-tags"></i>
                    Tags
                </h1>
                <p class="page-header__subtitle">Manage product tags</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.tags.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Tag</span>
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
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Tags
                </h3>
                <p class="modern-card__subtitle">{{ $tags->total() }} total tags</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search tags..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.tags.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($tags->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Name</th>
                                <th class="modern-table__th">Slug</th>
                                <th class="modern-table__th">Products</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($tags as $tag)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <strong>{{ $tag->name }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        <code>{{ $tag->slug }}</code>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge bg-primary">{{ $tag->products_count }}</span>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.tags.show', $tag) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tags.edit', $tag) }}"
                                               class="action-btn action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.tags.destroy', $tag) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this tag?')">
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

                @if($tags->hasPages())
                    <div class="pagination-wrapper">
                        {{ $tags->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="empty-state__title">No Tags Found</h3>
                    <p class="empty-state__text">Start by creating your first tag</p>
                    <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add Tag
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

