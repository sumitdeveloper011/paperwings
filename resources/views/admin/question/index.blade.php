@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-comments"></i>
                    Product Questions
                </h1>
                <p class="page-header__subtitle">Manage customer questions</p>
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
                <h3 class="modern-card__title">All Questions</h3>
                <p class="modern-card__subtitle">{{ $questions->total() }} total questions</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search questions..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.questions.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($questions->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Product</th>
                                <th class="modern-table__th">Question</th>
                                <th class="modern-table__th">Asked By</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th">Answers</th>
                                <th class="modern-table__th">Date</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($questions as $question)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <strong>{{ $question->product->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $question->question }}">
                                            {{ Str::limit($question->question, 60) }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">{{ $question->reviewer_name ?? $question->name }}</td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.questions.updateStatus', $question) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="0" {{ $question->status == 0 ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ $question->status == 1 ? 'selected' : '' }}>Approved</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge bg-info">{{ $question->answers->count() }}</span>
                                    </td>
                                    <td class="modern-table__td">
                                        {{ $question->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.questions.show', $question) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.questions.destroy', $question) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this question?')">
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

                @if($questions->hasPages())
                    <div class="pagination-wrapper">
                        {{ $questions->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="empty-state__title">No Questions Found</h3>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

