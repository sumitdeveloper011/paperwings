@if($subscriptions->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">ID</th>
                    <th class="modern-table__th">Email</th>
                    <th class="modern-table__th">Status</th>
                    <th class="modern-table__th">Subscribed At</th>
                    <th class="modern-table__th">Unsubscribed At</th>
                    <th class="modern-table__th">Created At</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($subscriptions as $subscription)
                <tr class="modern-table__row">
                    <td class="modern-table__td">#{{ $subscription->id }}</td>
                    <td class="modern-table__td">
                        <div class="email-info" style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-envelope" style="color: #6b7280;"></i>
                            <strong>{{ $subscription->email }}</strong>
                        </div>
                    </td>
                    <td class="modern-table__td">
                        @if($subscription->status == 1)
                            <span class="badge badge--success">Active</span>
                        @else
                            <span class="badge badge--danger">Inactive</span>
                        @endif
                    </td>
                    <td class="modern-table__td">
                        @if($subscription->subscribed_at)
                            {{ $subscription->subscribed_at->format('M d, Y H:i') }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="modern-table__td">
                        @if($subscription->unsubscribed_at)
                            {{ $subscription->unsubscribed_at->format('M d, Y H:i') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="modern-table__td">{{ $subscription->created_at->format('M d, Y') }}</td>
                    <td class="modern-table__td modern-table__td--actions">
                        <div class="action-buttons">
                            <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="action-btn action-btn--view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription) }}" 
                                  class="action-form" onsubmit="return confirm('Are you sure you want to delete this subscription?');">
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
@else
    <div class="empty-state">
        <i class="fas fa-envelope fa-3x"></i>
        <h3>No subscriptions found</h3>
        <p>There are no subscriptions matching your criteria.</p>
    </div>
@endif

