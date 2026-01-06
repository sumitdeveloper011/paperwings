@extends('layouts.admin.main')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header__content">
            <div class="page-header__title-section">
                <h1 class="page-header__title">
                    <i class="fas fa-envelope"></i>
                    Contact Messages
                </h1>
                <p class="page-header__subtitle">Manage customer inquiries and messages</p>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="modern-card">
        <div class="modern-card__header">
            <div class="modern-card__header-content">
                <h3 class="modern-card__title">
                    <i class="fas fa-list"></i>
                    All Messages
                </h3>
                <p class="modern-card__subtitle">{{ $messages->total() }} total messages</p>
            </div>
            <div class="modern-card__header-actions">
                <form method="GET" class="filter-form">
                    <div class="search-form__wrapper">
                        <i class="fas fa-search search-form__icon"></i>
                        <input type="text" name="search" class="search-form__input" 
                               placeholder="Search messages..." value="{{ $search }}">
                        @if($search)
                            <a href="{{ route('admin.contacts.index') }}" class="search-form__clear">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                    <select name="status" class="filter-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="solved" {{ $status == 'solved' ? 'selected' : '' }}>Solved</option>
                        <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>

        <div class="modern-card__body">
            @if($messages->count() > 0)
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead class="modern-table__head">
                            <tr>
                                <th class="modern-table__th">Name</th>
                                <th class="modern-table__th">Email</th>
                                <th class="modern-table__th">Subject</th>
                                <th class="modern-table__th">Status</th>
                                <th class="modern-table__th">Date</th>
                                <th class="modern-table__th modern-table__th--actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="modern-table__body">
                            @foreach($messages as $message)
                                <tr class="modern-table__row {{ !$message->admin_viewed_at ? 'modern-table__row--unread' : '' }}">
                                    <td class="modern-table__td">
                                        <strong>{{ $message->name }}</strong>
                                        @if($message->phone)
                                            <br><small class="text-muted">{{ $message->phone }}</small>
                                        @endif
                                    </td>
                                    <td class="modern-table__td">{{ $message->email }}</td>
                                    <td class="modern-table__td">
                                        <strong>{{ $message->subject }}</strong>
                                    </td>
                                    <td class="modern-table__td">
                                        {!! $message->status_badge !!}
                                    </td>
                                    <td class="modern-table__td">
                                        {{ $message->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $message->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td class="modern-table__td modern-table__td--actions">
                                        <a href="{{ route('admin.contacts.show', $message) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.contacts.destroy', $message) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this message?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $messages->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox empty-state__icon"></i>
                    <p class="empty-state__text">No contact messages found.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.filter-form {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.875rem;
    min-width: 150px;
    height: 38px;
}

.filter-form .btn {
    height: 38px;
    padding: 0.5rem 1rem;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
        width: 100%;
    }
    
    .filter-form .search-form__wrapper,
    .filter-form .filter-select,
    .filter-form .btn {
        width: 100%;
    }
}
</style>
@endsection

