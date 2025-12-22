@if($subscriptions->count() > 0)
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Subscribed At</th>
                    <th>Unsubscribed At</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $subscription)
                <tr>
                    <td>#{{ $subscription->id }}</td>
                    <td>
                        <div class="email-info">
                            <i class="fas fa-envelope"></i>
                            <strong>{{ $subscription->email }}</strong>
                        </div>
                    </td>
                    <td>
                        @if($subscription->status == 1)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($subscription->subscribed_at)
                            {{ $subscription->subscribed_at->format('M d, Y H:i') }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($subscription->unsubscribed_at)
                            {{ $subscription->unsubscribed_at->format('M d, Y H:i') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $subscription->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.subscriptions.destroy', $subscription) }}" 
                                  class="delete-form" onsubmit="return confirm('Are you sure you want to delete this subscription?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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

