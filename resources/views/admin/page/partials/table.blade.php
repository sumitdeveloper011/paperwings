@php
    $hasPages = false;
    if ($pages instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $hasPages = $pages->total() > 0;
    } else {
        $hasPages = $pages->count() > 0;
    }
@endphp

@if($hasPages)
    <div class="modern-table-wrapper modern-table-wrapper--enhanced">
        <table class="modern-table modern-table--enhanced">
            <thead class="modern-table__head modern-table__head--sticky">
                <tr>
                    <th class="modern-table__th" width="120">
                        <span>Image</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Title</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Sub Title</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Slug</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Status</span>
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
                @foreach($pages as $index => $page)
                    <tr class="modern-table__row modern-table__row--animated" style="animation-delay: {{ $index * 0.05 }}s;">
                        <td class="modern-table__td">
                            <div class="category-image category-image--enhanced">
                                <img src="{{ $page->thumbnail_url ?? asset('assets/images/placeholder.jpg') }}"
                                     alt="{{ $page->title }}"
                                     class="category-image__img"
                                     onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div class="category-name">
                                <strong>{{ $page->title }}</strong>
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <span class="text-muted">{{ $page->sub_title ?? '-' }}</span>
                        </td>
                        <td class="modern-table__td">
                            <code class="category-slug">{{ $page->slug }}</code>
                        </td>
                        <td class="modern-table__td">
                            <form method="POST" action="{{ route('admin.pages.updateStatus', $page) }}" class="status-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select" data-page-id="{{ $page->id }}">
                                    <option value="1" {{ (int)($page->status ?? 1) === 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ (int)($page->status ?? 1) === 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td">
                            <div class="category-date">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $page->created_at->format('M d, Y') }}
                            </div>
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons action-buttons--enhanced">
                                @can('pages.view')
                                <a href="{{ route('admin.pages.show', $page) }}"
                                   class="action-btn action-btn--view action-btn--ripple"
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('pages.edit')
                                <a href="{{ route('admin.pages.edit', $page) }}"
                                   class="action-btn action-btn--edit action-btn--ripple"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('pages.delete')
                                <form method="POST"
                                      action="{{ route('admin.pages.destroy', $page) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this page?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn--delete action-btn--ripple" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pages->hasPages())
        <div class="pagination-wrapper">
            {{ $pages->links('components.pagination') }}
        </div>
    @endif
@else
    <div class="empty-state empty-state--enhanced">
        <div class="empty-state__icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <h3 class="empty-state__title">No Pages Found</h3>
        @if(request()->get('search'))
            <p class="empty-state__text">No pages found matching "{{ request()->get('search') }}"</p>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-primary btn-ripple">
                <i class="fas fa-arrow-left"></i>
                View All Pages
            </a>
        @else
            <p class="empty-state__text">Start by creating your first page</p>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-ripple">
                <i class="fas fa-plus"></i>
                Add Page
            </a>
        @endif
    </div>
@endif
