@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Order Confirmed',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Order Confirmed', 'url' => null]
        ]
    ])

    <section class="checkout-success-section">
        <div class="container">
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="success-title">Order Confirmed!</h1>
                <p class="success-message">Thank you for your order. We've sent a confirmation email to <strong>{{ $order->billing_email }}</strong> with your order details and invoice.</p>

                <div class="order-summary-box">
                    <div class="order-info-row">
                        <span class="info-label">Order Number:</span>
                        <span class="info-value">{{ $order->order_number }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value">{{ $order->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="info-label">Total Amount:</span>
                        <span class="info-value">${{ number_format($order->total, 2) }}</span>
                    </div>
                    <div class="order-info-row">
                        <span class="info-label">Payment Status:</span>
                        <span class="info-value" style="text-transform: capitalize; color: #28a745;">{{ $order->payment_status }}</span>
                    </div>
                </div>

                <div class="success-actions">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary">Continue Shopping</a>
                    <a href="{{ route('account.my-orders') }}" class="btn btn-primary">View My Orders</a>
                </div>
            </div>
        </div>
    </section>

    <style>
        .checkout-success-section {
            padding: 4rem 0;
            background: #f8f9fa;
        }
        .success-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 1.5rem;
        }
        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .success-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .order-summary-box {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
            text-align: left;
        }
        .order-info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .order-info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .info-value {
            color: #2c3e50;
            font-weight: 600;
        }
        .success-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .success-actions .btn {
            padding: 0.75rem 2rem;
        }
    </style>

    @php
        try {
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
            $gaId = $settings['google_analytics_id'] ?? '';
            $gaEnabled = isset($settings['google_analytics_enabled']) && $settings['google_analytics_enabled'] == '1';
            $gaEcommerce = isset($settings['google_analytics_ecommerce']) && $settings['google_analytics_ecommerce'] == '1';
        } catch (\Exception $e) {
            $gaId = '';
            $gaEnabled = false;
            $gaEcommerce = false;
        }
    @endphp

    @if($gaEnabled && $gaEcommerce && !empty($gaId) && $order->payment_status === 'paid' && $order->items && $order->items->count() > 0)
    <!-- Google Analytics E-commerce Tracking -->
    <script>
        // Track purchase event
        gtag('event', 'purchase', {
            'transaction_id': '{{ $order->order_number }}',
            'value': {{ number_format($order->total, 2, '.', '') }},
            'currency': 'NZD',
            'tax': {{ number_format($order->tax ?? 0, 2, '.', '') }},
            'shipping': {{ number_format($order->shipping_price ?? $order->shipping ?? 0, 2, '.', '') }},
            'coupon': '{{ $order->coupon_code ?? '' }}',
            'items': [
                @foreach($order->items as $item)
                {
                    'item_id': '{{ $item->product_id }}',
                    'item_name': '{{ addslashes($item->product_name ?? 'Product') }}',
                    'item_category': '{{ isset($item->product->category) ? addslashes($item->product->category->name) : "Uncategorized" }}',
                    'price': {{ number_format($item->price ?? 0, 2, '.', '') }},
                    'quantity': {{ $item->quantity ?? 1 }}
                }@if(!$loop->last),@endif
                @endforeach
            ]
        });
    </script>
    @endif
@endsection
