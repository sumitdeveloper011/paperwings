@if($roles->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Name</th>
                    <th class="modern-table__th">Guard</th>
                    <th class="modern-table__th">Permissions</th>
                    <th class="modern-table__th">Users</th>
                    <th class="modern-table__th">Created</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($roles as $role)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <strong>{{ $role->name }}</strong>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge bg-secondary">{{ $role->guard_name }}</span>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge bg-info">{{ $role->permissions_count }} permissions</span>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge bg-primary">{{ $role->users_count }} users</span>
                        </td>
                        <td class="modern-table__td">
                            {{ $role->created_at->format('M d, Y') }}
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.roles.show', $role) }}"
                                   class="action-btn action-btn--view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.roles.edit', $role) }}"
                                   class="action-btn action-btn--edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($role->name !== 'SuperAdmin')
                                    <form method="POST"
                                          action="{{ route('admin.roles.destroy', $role) }}"
                                          class="action-form"
                                          onsubmit="return confirm('Are you sure you want to delete this role?')">
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
        <div class="empty-state__icon">
            <i class="fas fa-user-tag"></i>
        </div>
        <h3 class="empty-state__title">No Roles Found</h3>
        @if(request('search'))
            <p class="empty-state__text">No roles found matching "{{ request('search') }}"</p>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All Roles
            </a>
        @else
            <p class="empty-state__text">Start by creating your first role</p>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Role
            </a>
        @endif
    </div>
@endif
