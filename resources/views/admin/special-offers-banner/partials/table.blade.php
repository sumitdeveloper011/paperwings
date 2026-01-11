@if($banners->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Image</th>
                    <th class="modern-table__th">Title</th>
                    <th class="modern-table__th">Description</th>
                    <th class="modern-table__th">Button</th>
                    <th class="modern-table__th">Date Range</th>
                    <th class="modern-table__th">Status</th>
                    <th class="modern-table__th">Sort Order</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($banners as $banner)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            @if($banner->image_url)
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                     class="img-thumbnail" style="width: 80px; height: 50px; object-fit: cover;">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td class="modern-table__td">
                            <strong>{{ $banner->title }}</strong>
                        </td>
                        <td class="modern-table__td">
                            <div class="text-truncate" style="max-width: 200px;" title="{{ strip_tags($banner->description) }}">
                                {{ \Illuminate\Support\Str::limit(strip_tags($banner->description), 40) }}
                            </div>
                        </td>
                        <td class="modern-table__td">
                            @if($banner->button_text)
                                <span class="badge bg-info">{{ $banner->button_text }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="modern-table__td">
                            <small>
                                @if($banner->start_date)
                                    {{ $banner->start_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">No start</span>
                                @endif
                                <br>
                                @if($banner->end_date)
                                    {{ $banner->end_date->format('M d, Y') }}
                                @else
                                    <span class="text-muted">No end</span>
                                @endif
                            </small>
                        </td>
                        <td class="modern-table__td">
                            <form method="POST" action="{{ route('admin.special-offers-banners.updateStatus', $banner) }}" class="status-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select" data-banner-id="{{ $banner->uuid }}">
                                    @php
                                        $status = (int) $banner->status;
                                    @endphp
                                    <option value="1" {{ $status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td">{{ $banner->sort_order ?? 0 }}</td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                @can('special-offers.view')
                                <a href="{{ route('admin.special-offers-banners.show', $banner) }}"
                                   class="action-btn action-btn--view" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('special-offers.edit')
                                <a href="{{ route('admin.special-offers-banners.edit', $banner) }}"
                                   class="action-btn action-btn--edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('special-offers.delete')
                                <form method="POST"
                                      action="{{ route('admin.special-offers-banners.destroy', $banner) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this banner?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn--delete" title="Delete">
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
    <div class="empty-state">
        <div class="empty-state__icon">
            <i class="fas fa-bullhorn"></i>
        </div>
        <h3 class="empty-state__title">No Banners Found</h3>
        @if(request()->get('search'))
            <p class="empty-state__text">No banners found matching "{{ request()->get('search') }}"</p>
            <a href="{{ route('admin.special-offers-banners.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All Banners
            </a>
        @else
            <p class="empty-state__text">Start by creating your first special offers banner</p>
            @can('special-offers.create')
            <a href="{{ route('admin.special-offers-banners.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Banner
            </a>
            @endcan
        @endif
    </div>
@endif
