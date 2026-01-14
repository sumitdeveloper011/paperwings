@if($tags->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Name</th>
                    <th class="modern-table__th">Slug</th>
                    <th class="modern-table__th">Products</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($tags as $tag)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <strong>{{ $tag->name }}</strong>
                        </td>
                        <td class="modern-table__td">
                            <code>{{ $tag->slug }}</code>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge bg-primary">{{ $tag->products_count }}</span>
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.tags.edit', $tag) }}"
                                   class="action-btn action-btn--edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="action-btn action-btn--delete" 
                                        title="Delete"
                                        onclick="deleteTag('{{ route('admin.tags.destroy', $tag) }}', '{{ $tag->name }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
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
            <i class="fas fa-tags"></i>
        </div>
        <h3 class="empty-state__title">No Tags Found</h3>
        <p class="empty-state__text">Start by creating your first tag</p>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add Tag
        </a>
    </div>
@endif
