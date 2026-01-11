@if($users->count() > 0)
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Created</th>
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
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" onerror="this.onerror=null; this.src='{{ asset('assets/images/profile.png') }}';">
                                @else
                                    <img src="{{ asset('assets/images/profile.png') }}" alt="{{ $user->name }}">
                                @endif
                            </div>
                            <div>
                                <strong>{{ $user->name }}</strong>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->roles && $user->roles->count() > 0)
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary me-1 mb-1">{{ $role->name }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">No roles</span>
                        @endif
                    </td>
                    <td>{{ $user->phone ?? 'N/A' }}</td>
                    <td>
                        @if(!$user->hasRole('SuperAdmin'))
                        <form method="POST"
                              action="{{ route('admin.admin-users.updateStatus', $user->uuid) }}"
                              class="status-form ajax-status-form"
                              data-user-uuid="{{ $user->uuid }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="status-select">
                                @php
                                    $status = (int) $user->status;
                                @endphp
                                <option value="1" {{ $status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </form>
                        @else
                            <span class="badge bg-success">Active</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.admin-users.show', $user->uuid) }}" class="action-btn action-btn--view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!$user->hasRole('SuperAdmin') || Auth::user()->hasRole('SuperAdmin'))
                            <a href="{{ route('admin.admin-users.edit', $user->uuid) }}" class="action-btn action-btn--edit" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                            @if(!$user->hasRole('SuperAdmin'))
                                <form method="POST" action="{{ route('admin.admin-users.destroy', $user->uuid) }}"
                                      class="action-form" onsubmit="return confirm('Are you sure you want to delete this admin user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn--delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-user-shield fa-3x"></i>
        <h3>No admin users found</h3>
        <p>There are no admin users matching your criteria.</p>
    </div>
@endif
