@if($regions->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">
                        <span>Sr. No.</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Name</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Slug</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Shipping Price</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Status</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Created At</span>
                    </th>
                    <th class="modern-table__th modern-table__th--actions">
                        <span>Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($regions as $region)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <div class="sr-no">
                                {{ ($regions->currentPage() - 1) * $regions->perPage() + $loop->iteration }}
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div class="region-name">
                                <strong>{{ $region->name }}</strong>
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div class="region-slug">
                                <code class="code-badge">{{ $region->slug }}</code>
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div class="shipping-price-info">
                                @if($region->shippingPrice)
                                    <span class="badge bg-info">${{ number_format($region->shippingPrice->shipping_price, 2) }}</span>
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <form method="POST"
                                  action="{{ route('admin.regions.updateStatus', $region) }}"
                                  class="status-form ajax-status-form"
                                  data-region-uuid="{{ $region->uuid }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select">
                                    @php
                                        $status = (int) $region->status;
                                    @endphp
                                    <option value="1" {{ $status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
                        </td>
                        <td class="modern-table__td">
                            <div class="date-info">
                                <small>{{ $region->created_at->format('M d, Y') }}</small>
                            </div>
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                <a href="{{ route('admin.regions.show', $region->uuid) }}"
                                   class="action-btn action-btn--view"
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.regions.edit', $region->uuid) }}"
                                   class="action-btn action-btn--edit"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('admin.regions.destroy', $region->uuid) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this region?')">
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
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <h3 class="empty-state__title">No Regions Found</h3>
        <p class="empty-state__text">Start by creating your first region</p>
        <a href="{{ route('admin.regions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Add Region
        </a>
    </div>
@endif
