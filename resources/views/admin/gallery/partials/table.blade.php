@php
    $hasGalleries = false;
    if ($galleries instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $hasGalleries = $galleries->total() > 0;
    } else {
        $hasGalleries = $galleries->count() > 0;
    }
@endphp

@if($hasGalleries)
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
                        <span>Items</span>
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
                @foreach($galleries as $index => $gallery)
                    <tr class="modern-table__row modern-table__row--animated">
                        <td class="modern-table__td">
                            <div class="category-name">
                                <strong>{{ $gallery->name }}</strong>
                                <br>
                                <code class="category-slug gallery-slug-text">{{ $gallery->slug }}</code>
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge badge-primary">
                                {{ ucfirst($gallery->category) }}
                            </span>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge badge-info">
                                {{ $gallery->items_count ?? $gallery->items()->count() }} items
                            </span>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge badge-{{ $gallery->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($gallery->status) }}
                            </span>
                        </td>
                        <td class="modern-table__td">
                            <div class="category-date">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $gallery->created_at->format('M d, Y') }}
                            </div>
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons action-buttons--enhanced">
                                @can('galleries.view')
                                <a href="{{ route('admin.galleries.show', $gallery) }}"
                                   class="action-btn action-btn--view action-btn--ripple"
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('galleries.edit')
                                <a href="{{ route('admin.galleries.edit', $gallery) }}"
                                   class="action-btn action-btn--edit action-btn--ripple"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('galleries.delete')
                                <form method="POST"
                                      action="{{ route('admin.galleries.destroy', $gallery) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this gallery? All items will be deleted.')">
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
@else
    <div class="empty-state empty-state--enhanced">
        <div class="empty-state__icon">
            <i class="fas fa-images"></i>
        </div>
        <h3 class="empty-state__title">No Galleries Found</h3>
        @if(request()->get('search') || request()->get('category') || request()->get('status'))
            <p class="empty-state__text">No galleries found matching your search criteria</p>
            <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-primary btn-ripple">
                <i class="fas fa-arrow-left"></i>
                View All Galleries
            </a>
        @else
            <p class="empty-state__text">Start by creating your first gallery</p>
            @can('galleries.create')
            <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary btn-ripple">
                <i class="fas fa-plus"></i>
                Add Gallery
            </a>
            @endcan
        @endif
    </div>
@endif
