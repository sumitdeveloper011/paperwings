@if($orders->count() > 0)
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>
                        <div>
                            <strong>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</strong><br>
                            <small class="text-muted">{{ $order->billing_email }}</small>
                        </div>
                    </td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="badge badge--info">{{ $order->items_count ?? $order->items->count() ?? 0 }} items</span>
                    </td>
                    <td><strong>${{ number_format($order->total ?? 0, 2) }}</strong></td>
                    <td>
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
                    <td>
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
                    <td>
                        <div class="action-buttons">
                            @can('orders.view')
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan
                            @can('orders.delete')
                            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                                  class="delete-form" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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

