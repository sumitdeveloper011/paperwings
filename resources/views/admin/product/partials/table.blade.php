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
                        <span>Status</span>
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
                                                @php
                                                    $discountPercent = $product->total_price > 0 ? round((($product->total_price - $product->discount_price) / $product->total_price) * 100, 0) : 0;
                                                    $discountPriceWithoutTax = round($product->discount_price / 1.15, 2);
                                                @endphp
                                                -{{ $discountPercent }}%
                                            </span>
                                        </div>
                                        <small class="text-muted">Ex. Tax: ${{ number_format($discountPriceWithoutTax, 2) }}</small>
                                    </div>
                                @else
                                    <strong class="text-success">${{ number_format($product->total_price, 2) }}</strong>
                                    <br>
                                    <small class="text-muted">Ex. Tax: ${{ number_format($product->price_without_tax, 2) }}</small>
                                @endif
                            </div>
                        </td>
                        <td class="modern-table__td">
                            <form method="POST" action="{{ route('admin.products.updateStatus', $product) }}" class="status-form" data-product-id="{{ $product->id }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="status-select" data-product-id="{{ $product->id }}">
                                    <option value="1" {{ (int)$product->status === 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ (int)$product->status === 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </form>
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
                                @can('products.edit')
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="action-btn action-btn--edit"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('products.delete')
                                <form method="POST"
                                      action="{{ route('admin.products.destroy', $product) }}"
                                      class="action-form"
                                      onsubmit="return confirm('Are you sure you want to delete this product?')">
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
            <i class="fas fa-box-open"></i>
        </div>
        <h3 class="empty-state__title">No Products Found</h3>
        @if(request()->get('search'))
            <p class="empty-state__text">No products found matching "{{ request()->get('search') }}"</p>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All Products
            </a>
        @elseif(request()->get('category_id'))
            <p class="empty-state__text">No products found in selected category</p>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                View All Products
            </a>
        @else
            <p class="empty-state__text">Start by creating your first product</p>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Add Product
            </a>
        @endif
    </div>
@endif
