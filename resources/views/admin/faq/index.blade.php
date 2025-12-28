@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-question-circle"></i>
                    FAQs
                </h1>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-icon">
                    <i class="fas fa-plus"></i>
                    <span>Add FAQ</span>
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

    <!-- Main Content Card -->
    <div class="modern-card modern-card--compact">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All FAQs
                </h3>
                <p class="modern-card__subtitle">{{ $faqs->total() }} total FAQs</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="d-flex gap-2 align-items-center">
                    @if(count($categories) > 0)
                    <select name="category" class="form-control form-control-sm" style="width: 150px;" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $cat)) }}
                            </option>
                        @endforeach
                    </select>
                    @endif
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search FAQs..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.faqs.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($faqs->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Question</th>
                                <th class="modern-table__th">Answer</th>
                                <th class="modern-table__th">Category</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th">Sort Order</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($faqs as $faq)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <strong>{{ Str::limit($faq->question, 60) }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $faq->answer }}">
                                            {{ Str::limit(strip_tags($faq->answer), 50) }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">
                                        @if($faq->category)
                                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $faq->category)) }}</span>
                                        @else
                                            <span class="text-muted">General</span>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.faqs.updateStatus', $faq) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="1" {{ $faq->status ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !$faq->status ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">{{ $faq->sort_order ?? 0 }}</td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.faqs.show', $faq) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.faqs.edit', $faq) }}"
                                               class="action-btn action-btn--edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.faqs.destroy', $faq) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this FAQ?')">
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

                @if($faqs->hasPages())
                    <div class="pagination-wrapper">
                        {{ $faqs->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3 class="empty-state__title">No FAQs Found</h3>
                    <p class="empty-state__text">Start by creating your first FAQ</p>
                    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Add FAQ
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

