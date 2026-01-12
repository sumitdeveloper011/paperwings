@extends('layouts.frontend.main')
@section('content')
@include('frontend.partials.page-header', [
    'title' => 'My Orders',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'My Account', 'url' => route('account.view-profile')],
        ['label' => 'My Orders', 'url' => null]
    ]
])
<section class="account-section">
    <div class="container">
        <div class="row">
            @include('frontend.account.partials.sidebar')

            <!-- Account Content -->
            <div class="col-lg-9">
                <!-- My Orders -->
                <div class="account-content">
                    <div class="account-block">
                        <h2 class="account-block__title">My Orders / Order History</h2>

                        <div class="orders-list">
                            @if($orders && $orders->count() > 0)
                                @foreach($orders as $order)
                                <div class="order-history-item">
                                    <div class="order-history-item__header">
                                        <div class="order-history-item__info">
                                            <div class="order-history-item__number">
                                                <strong>Order #</strong>
                                                <span>{{ $order->order_number ?? 'ORD-' . $order->id }}</span>
                                            </div>
                                            <div class="order-history-item__date">
                                                <i class="fas fa-calendar"></i>
                                                <span>{{ $order->created_at->format('F d, Y') }}</span>
                                            </div>
                                            <div class="order-history-item__status">
                                                <span class="order-status order-status--{{ strtolower($order->status ?? 'pending') }}">{{ ucfirst($order->status ?? 'Pending') }}</span>
                                            </div>
                                        </div>
                                        <div class="order-history-item__total">
                                            <strong>Total: ${{ number_format($order->total ?? 0, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="order-history-item__body">
                                        <div class="order-history-item__products">
                                            @if(isset($order->items) && $order->items->count() > 0)
                                                @foreach($order->items as $item)
                                                <div class="order-product-mini">
                                                    @if($item->product)
                                                        <img src="{{ $item->product->main_thumbnail_url ?? asset('assets/images/placeholder.jpg') }}" alt="{{ $item->product->name ?? $item->product_name }}">
                                                        <span class="order-product-mini__name">{{ $item->product->name ?? $item->product_name }}</span>
                                                    @else
                                                        <img src="{{ asset('assets/images/placeholder.jpg') }}" alt="{{ $item->product_name }}">
                                                        <span class="order-product-mini__name">{{ $item->product_name }}</span>
                                                    @endif
                                                    <span class="order-product-mini__qty">x{{ $item->quantity }}</span>
                                                </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">No items found for this order.</p>
                                            @endif
                                        </div>
                                        <div class="order-history-item__actions">
                                            <a href="{{ route('account.order-details', $order->order_number) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </a>
                                            @if(($order->status ?? 'pending') == 'pending' || ($order->status ?? 'pending') == 'processing')
                                            <button class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-times"></i>
                                                Cancel Order
                                            </button>
                                            @else
                                            <button class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-redo"></i>
                                                Reorder
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Pagination -->
                                @if($orders->hasPages())
                                <div class="mt-4">
                                    {{ $orders->links() }}
                                </div>
                                @endif
                            @else
                                <div class="orders-list__empty">
                                    <div class="text-center py-5">
                                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-3">You haven't placed any orders yet.</p>
                                        <a href="{{ route('home') }}" class="btn btn-primary">Start Shopping</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
