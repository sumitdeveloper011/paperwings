@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-reply"></i>
                    Product Answers
                </h1>
                <p class="page-header__subtitle">Manage customer answers</p>
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
                <h3 class="modern-card__title">All Answers</h3>
                <p class="modern-card__subtitle">{{ $answers->total() }} total answers</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="search-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input"
                               placeholder="Search answers..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.answers.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($answers->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Question</th>
                                <th class="modern-table__th">Answer</th>
                                <th class="modern-table__th">Answered By</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th">Helpful</th>
                                <th class="modern-table__th">Date</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($answers as $answer)
                                <tr class="modern-table__row">
                                    <td class="modern-table__td">
                                        <strong>{{ Str::limit($answer->question->question ?? 'N/A', 40) }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        <div class="text-truncate" style="max-width: 300px;" title="{{ $answer->answer }}">
                                            {{ Str::limit($answer->answer, 60) }}
                                        </div>
                                    </td>
                                    <td class="modern-table__td">{{ $answer->reviewer_name ?? $answer->name }}</td>
                                    <td class="modern-table__td">
                                        <form method="POST" action="{{ route('admin.answers.updateStatus', $answer) }}" class="status-form">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="status-select" onchange="this.form.submit()">
                                                <option value="0" {{ $answer->status == 0 ? 'selected' : '' }}>Pending</option>
                                                <option value="1" {{ $answer->status == 1 ? 'selected' : '' }}>Approved</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="modern-table__td">
                                        <span class="badge bg-info">{{ $answer->helpful_count }}</span>
                                    </td>
                                    <td class="modern-table__td">
                                        {{ $answer->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.answers.show', $answer) }}"
                                               class="action-btn action-btn--view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('admin.answers.destroy', $answer) }}"
                                                  class="action-form"
                                                  onsubmit="return confirm('Are you sure you want to delete this answer?')">
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

                @if($answers->hasPages())
                    <div class="pagination-wrapper">
                        {{ $answers->links('components.pagination') }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state__icon">
                        <i class="fas fa-reply"></i>
                    </div>
                    <h3 class="empty-state__title">No Answers Found</h3>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

