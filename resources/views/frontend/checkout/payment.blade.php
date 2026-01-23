@extends('layouts.frontend.main')

@push('head')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Checkout - Payment',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Cart', 'url' => route('cart.index')],
            ['label' => 'Checkout', 'url' => route('checkout.details')],
            ['label' => 'Review', 'url' => route('checkout.review')],
            ['label' => 'Payment', 'url' => route('checkout.payment')]
        ]
    ])

    <section class="checkout-section">
        <div class="container">
            <form method="POST" action="{{ route('checkout.process-payment') }}" id="checkoutForm">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="checkout-payment-block">
                            <h2 class="checkout-payment-block__title">Complete Payment</h2>

                            <div class="checkout-payment-block__final-total">
                                <span>Total Amount</span>
                                <span class="checkout-payment-block__final-total-amount" id="paymentFinalTotal">${{ number_format($totals['total'], 2) }}</span>
                            </div>

                            <div id="payment-element-container">
                                <div id="payment-loading" style="text-align: center; padding: 40px 20px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0;">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #007bff; margin-bottom: 15px;"></i>
                                    <p style="color: #6c757d; font-size: 0.95rem; margin: 0;">Loading payment options...</p>
                                </div>
                                <div id="payment-element" style="display: none; min-height: 200px;"></div>
                                <div id="payment-errors" role="alert" style="color: #dc3545; margin-top: 10px; font-size: 0.9rem; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; display: none;"></div>
                            </div>

                            <div class="checkout-payment-block__security">
                                <i class="fas fa-lock"></i>
                                <span>Your payment information is secure and encrypted</span>
                            </div>

                            <div class="checkout-payment-block__actions">
                                <a href="{{ route('checkout.review') }}" class="checkout-payment-block__btn checkout-payment-block__btn--secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Review
                                </a>
                                <button type="submit" class="checkout-payment-block__btn checkout-payment-block__btn--primary" id="placeOrderBtn" disabled>
                                    <span id="placeOrderBtnText">Loading Payment Options...</span>
                                    <span id="placeOrderSpinner" style="display: none;"><i class="fas fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="checkout-payment-summary">
                            <h3 class="checkout-payment-summary__title">Order Summary</h3>
                            
                            <div class="checkout-payment-summary__items">
                                @foreach($cartItems as $cartItem)
                                <div class="checkout-payment-summary__item">
                                    <div class="checkout-payment-summary__item-image">
                                        <img src="{{ $cartItem->product->main_thumbnail_url }}" alt="{{ $cartItem->product->name }}" onerror="this.src='{{ asset('assets/images/placeholder.jpg') }}'">
                                    </div>
                                    <div class="checkout-payment-summary__item-info">
                                        <h4 class="checkout-payment-summary__item-name">{{ $cartItem->product->name }}</h4>
                                        <div class="checkout-payment-summary__item-details">
                                            <span>${{ number_format($cartItem->price, 2) }} x {{ $cartItem->quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="checkout-payment-summary__totals">
                                <div class="checkout-payment-summary__total-row">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($totals['subtotal'], 2) }}</span>
                                </div>
                                @if($appliedCoupon && $totals['discount'] > 0)
                                <div class="checkout-payment-summary__total-row checkout-payment-summary__total-row--discount">
                                    <span>Discount ({{ $appliedCoupon['code'] }})</span>
                                    <span>-${{ number_format($totals['discount'], 2) }}</span>
                                </div>
                                @endif
                                <div class="checkout-payment-summary__total-row" style="{{ $totals['shipping'] == 0 ? 'display: none;' : '' }}">
                                    <span>Shipping</span>
                                    <span>${{ number_format($totals['shipping'], 2) }}</span>
                                </div>
                                <div class="checkout-payment-summary__total-row checkout-payment-summary__total-row--final">
                                    <span>Total</span>
                                    <span>${{ number_format($totals['total'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="{{ asset('assets/frontend/js/checkout/stripe-payment.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Analytics && window.Analytics.isEnabled()) {
                window.Analytics.trackCheckoutStep('payment', 3);
            }

            new StripePaymentHandler({
                stripeKey: @json($stripePublishableKey),
                clientSecret: @json($clientSecret ?? null),
                paymentIntentId: @json($paymentIntentId ?? null),
                createPaymentIntentUrl: '{{ route("checkout.create-payment-intent") }}',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                total: {{ $totals['final_total'] ?? $totals['total'] }}
            });
        });
    </script>
@endsection
