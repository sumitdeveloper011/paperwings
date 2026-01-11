@php
    $hasBundles = false;
    if ($bundles instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $hasBundles = $bundles->total() > 0;
    } else {
        $hasBundles = $bundles->count() > 0;
    }
@endphp

@if($hasBundles)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">
                        <span>Image</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Name</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Products</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Bundle Price</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Discount</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Status</span>
                    </th>
                    <th class="modern-table__th modern-table__th--actions">
                        <span>Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($bundles as $bundle)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            @if($bundle->images && $bundle->images->count() > 0)
                                <img src="{{ $bundle->images->first()->image_url }}"
                                     alt="{{ $bundle->name }}"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;"
                                     onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                            @else
                                <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            @endif
                        </td>
                        <td class="modern-table__td">
                            <div class="category-name">
                                <strong>{{ $bundle->name }}</strong>
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <span class="badge badge--info">{{ $bundle->products_count }}</span>
                        </td>
                        <td class="modern-table__td">
                            <strong>${{ number_format($bundle->bundle_price, 2) }}</strong>
                        </td>
                        <td class="modern-table__td">
                            @if($bundle->discount_percentage)
                                <span class="badge badge--success">{{ $bundle->discount_percentage }}%</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="modern-table__td">
                            <form method="POST" action="{{ route('admin.bundles.updateStatus', $bundle) }}" class="status-form">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select" data-bundle-id="{{ $bundle->id }}">
                                    <option value="1" {{ $bundle->status ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$bundle->status ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.bundles.show', $bundle) }}"
                                   class="action-btn action-btn--view"
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.bundles.edit', $bundle) }}"
                                   class="action-btn action-btn--edit"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.bundles.destroy', $bundle) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this bundle?')">
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
            <i class="fas fa-boxes"></i>
        </div>
        <h3 class="empty-state__title">No Bundles Found</h3>
        @if(request()->get('search'))
            <p class="empty-state__text">No bundles found matching "{{ request()->get('search') }}"</p>
            <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All Bundles
            </a>
        @else
            <p class="empty-state__text">Start by creating your first bundle</p>
            <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Bundle
            </a>
        @endif
    </div>
@endif
