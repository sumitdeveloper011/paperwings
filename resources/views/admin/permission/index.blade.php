@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-key"></i>
                    Permissions
                </h1>
                <p class="page-header__subtitle">Manage system permissions</p>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add Permission</span>
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

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="modern-card modern-card--compact">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Permissions
                </h3>
                <p class="modern-card__subtitle">{{ $permissions->total() }} total permissions</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search permissions..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.permissions.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($permissions->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Name</th>
                                <th class="modern-table__th">Module</th>
                                <th class="modern-table__th">Guard</th>
                                <th class="modern-table__th">Roles</th>
                                <th class="modern-table__th">Created</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($permissions as $permission)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <strong>{{ $permission->name }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        @php
                                            $parts = explode('.', $permission->name);
                                            $module = $parts[0] ?? 'other';
                                        @endphp
                                        <span class="badge bg-secondary">{{ ucfirst($module) }}</span>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge bg-info">{{ $permission->guard_name }}</span>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge bg-primary">{{ $permission->roles_count }} roles</span>
                                    </td>
                                    <td class="modern-table__td">
                                        {{ $permission->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.permissions.show', $permission) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.permissions.edit', $permission) }}"
                                               class="action-btn action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.permissions.destroy', $permission) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this permission?')">
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

                @if($permissions->hasPages())
                    <div class="pagination-wrapper">
                        {{ $permissions->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h3 class="empty-state__title">No Permissions Found</h3>
                    @if($search)
                        <p class="empty-state__text">No permissions found matching "{{ $search }}"</p>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            View All Permissions
                        </a>
                    @else
                        <p class="empty-state__text">Start by creating your first permission</p>
                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Permission
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

