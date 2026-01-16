@if($users->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">ID</th>
                    <th class="modern-table__th">Name</th>
                    <th class="modern-table__th">Email</th>
                    <th class="modern-table__th">Roles</th>
                    <th class="modern-table__th">Phone</th>
                    <th class="modern-table__th" style="min-width: 80px;">Orders</th>
                    <th class="modern-table__th">Status</th>
                    <th class="modern-table__th">Joined</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($users as $user)
                <tr class="modern-table__row">
                    <td class="modern-table__td">#{{ $user->id }}</td>
                    <td class="modern-table__td">
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
                    <td class="modern-table__td">{{ $user->email }}</td>
                    <td class="modern-table__td">
                        @if($user->roles && $user->roles->count() > 0)
                            @foreach($user->roles as $role)
                                <span class="badge badge--primary me-1 mb-1">{{ $role->name }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">No roles</span>
                        @endif
                    </td>
                    <td class="modern-table__td">{{ $user->phone ?? ($user->userDetail->phone ?? 'N/A') }}</td>
                    <td class="modern-table__td">
                        <span class="badge badge--info">
                            {{ $user->orders_count ?? 0 }}
                        </span>
                    </td>
                    <td class="modern-table__td">
                        <form method="POST" action="{{ route('admin.users.updateStatus', $user) }}" class="status-form">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="status-select" data-user-id="{{ $user->id }}">
                                <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </form>
                    </td>
                    <td class="modern-table__td">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="modern-table__td modern-table__td--actions">
                        <div class="action-buttons">
                            @can('users.view')
                            <a href="{{ route('admin.users.show', $user) }}" class="action-btn action-btn--view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            @can('users.edit')
                            <a href="{{ route('admin.users.edit', $user) }}" class="action-btn action-btn--edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            @can('users.delete')
                            @if(!$user->hasRole('SuperAdmin'))
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      class="action-form" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn--delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                            @endcan
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

