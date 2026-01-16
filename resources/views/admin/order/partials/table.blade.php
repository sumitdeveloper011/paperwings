@if($orders->count() > 0)
    <div class="modern-table-wrapper">
        <table class="modern-table">
            <thead class="modern-table__head">
                <tr>
                    <th class="modern-table__th">Order #</th>
                    <th class="modern-table__th">Customer</th>
                    <th class="modern-table__th">Date</th>
                    <th class="modern-table__th">Items</th>
                    <th class="modern-table__th">Total</th>
                    <th class="modern-table__th">Status</th>
                    <th class="modern-table__th">Payment</th>
                    <th class="modern-table__th modern-table__th--actions">Actions</th>
                </tr>
            </thead>
            <tbody class="modern-table__body">
                @foreach($orders as $order)
                <tr class="modern-table__row">
                    <td class="modern-table__td"><strong>{{ $order->order_number }}</strong></td>
                    <td class="modern-table__td">
                        <div>
                            <strong>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</strong><br>
                            <small class="text-muted">{{ $order->billing_email }}</small>
                        </div>
                    </td>
                    <td class="modern-table__td">{{ $order->created_at->format('M d, Y') }}</td>
                    <td class="modern-table__td">
                        <span class="badge badge--info">{{ $order->items_count ?? $order->items->count() ?? 0 }} items</span>
                    </td>
                    <td class="modern-table__td"><strong>${{ number_format($order->total ?? 0, 2) }}</strong></td>
                    <td class="modern-table__td">
                        @php
                            $orderStatus = $order->status ?? null;
                            $statusBadgeClass = 'badge--warning';
                            if ($orderStatus === 'delivered') {
                                $statusBadgeClass = 'badge--success';
                            } elseif ($orderStatus === 'cancelled') {
                                $statusBadgeClass = 'badge--danger';
                            } elseif ($orderStatus === 'shipped') {
                                $statusBadgeClass = 'badge--info';
                            }
                        @endphp
                        @if($orderStatus)
                        <span class="badge {{ $statusBadgeClass }}">
                            {{ ucfirst($orderStatus) }}
                        </span>
                        @else
                        <span class="badge badge--secondary">N/A</span>
                        @endif
                    </td>
                    <td class="modern-table__td">
                        @php
                            $paymentStatus = $order->payment_status ?? null;
                            $paymentBadgeClass = 'badge--warning';
                            if ($paymentStatus === 'paid') {
                                $paymentBadgeClass = 'badge--success';
                            } elseif ($paymentStatus === 'failed') {
                                $paymentBadgeClass = 'badge--danger';
                            } elseif ($paymentStatus === 'refunded') {
                                $paymentBadgeClass = 'badge--secondary';
                            }
                        @endphp
                        @if($paymentStatus)
                        <span class="badge {{ $paymentBadgeClass }}">
                            {{ ucfirst($paymentStatus) }}
                        </span>
                        @else
                        <span class="badge badge--secondary">N/A</span>
                        @endif
                    </td>
                    <td class="modern-table__td modern-table__td--actions">
                        <div class="action-buttons">
                            @can('orders.view')
                            <a href="{{ route('admin.orders.show', $order) }}" class="action-btn action-btn--view" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            @can('orders.delete')
                            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                                  class="action-form" onsubmit="return confirm('Are you sure you want to delete this order?');">
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
        <i class="fas fa-shopping-cart fa-3x"></i>
        <h3>No orders found</h3>
        <p>There are no orders matching your criteria.</p>
    </div>
@endif

