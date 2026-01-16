@php
    $hasTemplates = false;
    if ($templates instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $hasTemplates = $templates->total() > 0;
    } else {
        $hasTemplates = $templates->count() > 0;
    }
@endphp

@if($hasTemplates)
    <div class="modern-table-wrapper modern-table-wrapper--enhanced">
        <table class="modern-table modern-table--enhanced">
            <thead class="modern-table__head modern-table__head--sticky">
                <tr>
                    <th class="modern-table__th">
                        <span>Name</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Category</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Subject</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Status</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Version</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Created</span>
                    </th>
                    <th class="modern-table__th modern-table__th--actions">
                        <span>Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($templates as $index => $template)
                    <tr class="modern-table__row modern-table__row--animated" style="animation-delay: {{ $index * 0.05 }}s;">
                        <td class="modern-table__td">
                            <div class="category-name">
                                <strong>{{ $template->name }}</strong>
                                <br>
                                <code class="category-slug" style="font-size: 0.85rem;">{{ $template->slug }}</code>
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge badge--{{ $template->category === 'system' ? 'danger' : ($template->category === 'order' ? 'primary' : ($template->category === 'user' ? 'info' : 'warning')) }}">
                                {{ ucfirst($template->category) }}
                            </span>
                        </td>
                        <td class="modern-table__td">
                            <span class="text-muted" style="max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: normal; word-wrap: break-word;">
                                {{ $template->subject }}
                            </span>
                        </td>
                        <td class="modern-table__td">
                            <form method="POST" action="{{ route('admin.email-templates.updateStatus', $template) }}" class="status-form">
                                @csrf
                                @method('PATCH')
                                <select name="is_active" class="status-select" data-template-id="{{ $template->id }}">
                                    <option value="1" {{ $template->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$template->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge badge--info">v{{ $template->version }}</span>
                        </td>
                        <td class="modern-table__td">
                            <div class="category-date">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $template->created_at->format('M d, Y') }}
                            </div>
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons action-buttons--enhanced">
                                @can('email-templates.view')
                                <a href="{{ route('admin.email-templates.show', $template) }}"
                                   class="action-btn action-btn--view action-btn--ripple"
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('email-templates.edit')
                                <a href="{{ route('admin.email-templates.edit', $template) }}"
                                   class="action-btn action-btn--edit action-btn--ripple"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('email-templates.edit')
                                <form method="POST"
                                      action="{{ route('admin.email-templates.duplicate', $template) }}"
                                      class="action-form"
                                      style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="action-btn action-btn--info action-btn--ripple" title="Duplicate">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                                @endcan
                                @can('email-templates.delete')
                                @if($template->category !== 'system')
                                <form method="POST"
                                      action="{{ route('admin.email-templates.destroy', $template) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn--delete action-btn--ripple" title="Delete">
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
    <div class="empty-state empty-state--enhanced">
        <div class="empty-state__icon">
            <i class="fas fa-envelope"></i>
        </div>
        <h3 class="empty-state__title">No Templates Found</h3>
        @if(request()->get('search') || request()->get('category'))
            <p class="empty-state__text">No templates found matching your search criteria</p>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-primary btn-ripple">
                <i class="fas fa-arrow-left"></i>
                View All Templates
            </a>
        @else
            <p class="empty-state__text">Start by creating your first email template</p>
            @can('email-templates.create')
            <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary btn-ripple">
                <i class="fas fa-plus"></i>
                Add Template
            </a>
            @endcan
        @endif
    </div>
@endif
