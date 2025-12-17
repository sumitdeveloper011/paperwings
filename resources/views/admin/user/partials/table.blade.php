@if($users->count() > 0)
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>#{{ $user->id }}</td>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                                @else
                                    <div class="user-avatar-placeholder">
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->userDetail->phone ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-info">
                            {{ Order::where('user_id', $user->id)->count() }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="status-form">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </form>
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                  class="delete-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
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
        <i class="fas fa-users fa-3x"></i>
        <h3>No users found</h3>
        <p>There are no users matching your criteria.</p>
    </div>
@endif

