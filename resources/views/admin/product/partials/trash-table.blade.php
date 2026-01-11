@php
    $hasProducts = false;
    if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $hasProducts = $products->total() > 0;
    } else {
        $hasProducts = $products->count() > 0;
    }
@endphp

@if($hasProducts)
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
                        <span>Category</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Price</span>
                    </th>
                    <th class="modern-table__th">
                        <span>Deleted At</span>
                    </th>
                    <th class="modern-table__th modern-table__th--actions">
                        <span>Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($products as $product)
                    <tr class="modern-table__row">
                        <td class="modern-table__td">
                            <div class="category-image">
                                <img src="{{ $product->main_image }}"
                                     alt="{{ $product->name }}"
                                     class="category-image__img"
                                     onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div class="category-name">
                                <strong>{{ $product->name }}</strong>
                                @if($product->short_description)
                                    <br>
                                    <small class="text-muted">{{ Str::limit(strip_tags($product->short_description), 50) }}</small>
                                @endif
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div>
                                @if($product->category)
                                    <span class="badge badge--info">
                                        <i class="fas fa-tag"></i>
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">Uncategorized</span>
                                @endif
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div>
                                @if($product->discount_price)
                                    <div class="price-with-discount">
                                        <div class="price-original">
                                            <strong class="text-muted" style="text-decoration: line-through; font-size: 0.875rem;">
                                                ${{ number_format($product->total_price, 2) }}
                                            </strong>
                                        </div>
                                        <div class="price-discounted">
                                            <strong class="text-success" style="font-size: 1rem;">
                                                ${{ number_format($product->discount_price, 2) }}
                                            </strong>
                                            <span class="discount-badge">
                                                -{{ round((($product->total_price - $product->discount_price) / $product->total_price) * 100, 0) }}%
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <strong>${{ number_format($product->total_price, 2) }}</strong>
                                @endif
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <div>
                                <small class="text-muted">
                                    {{ $product->deleted_at->format('M d, Y') }}<br>
                                    {{ $product->deleted_at->format('g:i A') }}
                                </small>
                                @php
                                    $orderItemsCount = \App\Models\OrderItem::where('product_id', $product->id)->count();
                                @endphp
                                @if($orderItemsCount > 0)
                                    <br>
                                    <small class="text-warning" style="font-weight: 600;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        In {{ $orderItemsCount }} order(s)
                                    </small>
                                @endif
                            </div>
                        </td>
                        <td class="modern-table__td modern-table__td--actions">
                            <div class="action-buttons">
                                @can('products.view')
                                <a href="{{ route('admin.products.show', $product) }}"
                                   class="action-btn action-btn--view"
                                   title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                @can('products.delete')
                                <form method="POST"
                                      action="{{ route('admin.products.restore', $product) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to restore this product?')">
                                    @csrf
                                    <button type="submit" class="action-btn action-btn--success" title="Restore">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                @php
                                    $orderItemsCount = \App\Models\OrderItem::where('product_id', $product->id)->count();
                                @endphp
                                @if($orderItemsCount == 0)
                                <form method="POST"
                                      action="{{ route('admin.products.forceDelete', $product) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this product? This action cannot be undone!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn--danger" title="Permanently Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @else
                                <button type="button" 
                                        class="action-btn action-btn--danger" 
                                        title="Cannot delete: Product is in {{ $orderItemsCount }} order(s)"
                                        style="opacity: 0.5; cursor: not-allowed;"
                                        disabled>
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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
    <div class="empty-state">
        <div class="empty-state__icon">
            <i class="fas fa-trash"></i>
        </div>
        <h3 class="empty-state__title">No Deleted Products</h3>
        @if(request()->get('search'))
            <p class="empty-state__text">No deleted products found matching "{{ request()->get('search') }}"</p>
            <a href="{{ route('admin.products.trash') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All Deleted Products
            </a>
        @else
            <p class="empty-state__text">Trash is empty</p>
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Products
            </a>
        @endif
    </div>
@endif

<style>
/* Discount Badge Styles */
.price-with-discount {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.price-original {
    line-height: 1.2;
}

.price-discounted {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    line-height: 1.2;
}

.discount-badge {
    display: inline-block;
    background: var(--danger-color);
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1.2;
}

.action-btn--success {
    background: var(--success-color);
    color: white;
}

.action-btn--success:hover {
    background: var(--success-color-dark);
    color: white;
}
</style>
