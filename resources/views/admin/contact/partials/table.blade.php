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
                            <a href="{{ route('admin.contacts.show', $message->uuid) }}"
                               class="btn btn-sm btn-info"
                               title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.contacts.destroy', $message->uuid) }}"
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
@else
    <div class="empty-state">
        <i class="fas fa-inbox empty-state__icon"></i>
        <p class="empty-state__text">No contact messages found.</p>
    </div>
@endif
