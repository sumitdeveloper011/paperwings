@extends('layouts.frontend.main')
@section('content')
    @include('frontend.partials.page-header', [
        'title' => 'Processing Payment',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Processing Payment', 'url' => null]
        ]
    ])

    <section class="checkout-processing-section">
        <div class="container">
            <div class="processing-container" style="max-width: 600px; margin: 2rem auto; text-align: center;">
                <div class="processing-icon" style="margin-bottom: 2rem;">
                    <div class="spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 60px; height: 60px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
                </div>
                <h1 class="processing-title" style="font-size: 2rem; margin-bottom: 1rem; color: #333;">Processing Your Payment</h1>
                <p class="processing-message" style="font-size: 1.1rem; color: #666; margin-bottom: 2rem;">
                    Please wait while we confirm your payment. This usually takes a few seconds.
                </p>
                
                <div class="processing-status" style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 1.5rem; margin-bottom: 2rem;">
                    <div class="status-item" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="color: #666;">Payment Intent ID:</span>
                        <code style="background: #fff; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.9rem;">{{ $paymentIntent }}</code>
                    </div>
                    <div class="status-message" style="color: #0066cc; font-weight: 500;">
                        <i class="fas fa-clock" style="margin-right: 0.5rem;"></i>
                        <span id="statusText">Waiting for payment confirmation...</span>
                    </div>
                </div>

                <div class="alert alert-info" style="margin: 1.5rem 0; padding: 1rem; background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; text-align: left; display: none;" id="infoAlert">
                    <i class="fas fa-info-circle" style="color: #0066cc; margin-right: 0.5rem;"></i>
                    <div style="display: inline-block;">
                        <p style="margin: 0; color: #0066cc; font-size: 0.95rem;" id="infoText">
                            Please do not close this page or refresh until payment is confirmed.
                        </p>
                    </div>
                </div>

                <div class="processing-actions" style="margin-top: 2rem;">
                    <a href="{{ route('checkout.payment') }}" class="btn btn-outline-secondary" id="backButton" style="display: none;">Back to Payment</a>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script>
        (function() {
            const paymentIntentId = '{{ $paymentIntent }}';
            const checkStatusUrl = '{{ route("checkout.check-payment-status", ["paymentIntentId" => ":id"]) }}'.replace(':id', paymentIntentId);
            const statusText = document.getElementById('statusText');
            const infoAlert = document.getElementById('infoAlert');
            const infoText = document.getElementById('infoText');
            const backButton = document.getElementById('backButton');
            let pollCount = 0;
            const maxPolls = 120;
            let pollInterval;

            function checkPaymentStatus() {
                pollCount++;
                
                if (pollCount > maxPolls) {
                    statusText.textContent = 'Payment confirmation is taking longer than expected. Please contact support.';
                    statusText.style.color = '#dc3545';
                    infoText.textContent = 'If you have already completed payment, please contact our support team with your Payment Intent ID: ' + paymentIntentId;
                    infoAlert.style.display = 'block';
                    backButton.style.display = 'inline-block';
                    clearInterval(pollInterval);
                    return;
                }

                fetch(checkStatusUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.data.order_exists && data.data.payment_status === 'paid') {
                            statusText.textContent = 'Payment confirmed! Redirecting...';
                            statusText.style.color = '#28a745';
                            
                            if (data.data.redirect_url) {
                                setTimeout(() => {
                                    window.location.href = data.data.redirect_url;
                                }, 1000);
                            } else {
                                window.location.reload();
                            }
                            clearInterval(pollInterval);
                        } else if (data.data.order_exists && data.data.payment_status === 'failed') {
                            statusText.textContent = 'Payment failed. Please try again.';
                            statusText.style.color = '#dc3545';
                            infoText.textContent = 'Your payment could not be processed. Please try again or use a different payment method.';
                            infoAlert.style.display = 'block';
                            infoAlert.style.background = '#fff3cd';
                            infoAlert.style.borderColor = '#ffc107';
                            backButton.style.display = 'inline-block';
                            clearInterval(pollInterval);
                        } else {
                            statusText.textContent = data.data.message || 'Waiting for payment confirmation...';
                        }
                    } else {
                        console.error('Error checking payment status:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                    if (pollCount > 10) {
                        statusText.textContent = 'Error checking payment status. Please refresh the page.';
                        statusText.style.color = '#dc3545';
                        backButton.style.display = 'inline-block';
                    }
                });
            }

            pollInterval = setInterval(checkPaymentStatus, 2000);
            checkPaymentStatus();
        })();
    </script>
@endsection
