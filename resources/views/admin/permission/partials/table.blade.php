@if($permissions->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Name</th>
                    <th class="modern-table__th">Module</th>
                    <th class="modern-table__th">Guard</th>
                    <th class="modern-table__th">Roles</th>
                    <th class="modern-table__th">Created</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($permissions as $permission)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <strong>{{ $permission->name }}</strong>
                        </td>
                        <td class="modern-table__td">
                            @php
                                $parts = explode('.', $permission->name);
                                $module = $parts[0] ?? 'other';
                            @endphp
                            <span class="badge bg-secondary">{{ ucfirst($module) }}</span>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge bg-info">{{ $permission->guard_name }}</span>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge bg-primary">{{ $permission->roles_count }} roles</span>
                        </td>
                        <td class="modern-table__td">
                            {{ $permission->created_at->format('M d, Y') }}
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.permissions.show', $permission) }}"
                                   class="action-btn action-btn--view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.permissions.edit', $permission) }}"
                                   class="action-btn action-btn--edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.permissions.destroy', $permission) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this permission?')">
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
        <div class="empty-state__icon">
            <i class="fas fa-key"></i>
        </div>
        <h3 class="empty-state__title">No Permissions Found</h3>
        @if(request('search'))
            <p class="empty-state__text">No permissions found matching "{{ request('search') }}"</p>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All Permissions
            </a>
        @else
            <p class="empty-state__text">Start by creating your first permission</p>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Permission
            </a>
        @endif
    </div>
@endif
