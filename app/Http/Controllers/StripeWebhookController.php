<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Mail\OrderConfirmationMail;
use App\Services\ShippingService;
use App\Services\PriceCalculationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    // Handle Stripe webhook events
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        // Read Stripe keys from .env config
        $endpointSecret = config('services.stripe.webhook_secret');
        $stripeSecret = config('services.stripe.secret');

        if (!$endpointSecret) {
            Log::error('Stripe webhook secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        if (!$stripeSecret) {
            Log::error('Stripe secret key not configured');
            return response()->json(['error' => 'Stripe secret not configured'], 500);
        }

        $event = null;

        try {
            Stripe::setApiKey($stripeSecret);
            Log::info('Attempting to construct webhook event', [
                'payload_length' => strlen($payload),
                'signature_header_present' => !empty($sigHeader)
            ]);
            
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            
            Log::info('Webhook event constructed successfully', [
                'event_id' => $event->id ?? 'N/A',
                'event_type' => $event->type ?? 'N/A',
                'event_created' => $event->created ?? 'N/A'
            ]);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', [
                'error' => $e->getMessage(),
                'payload_preview' => substr($payload, 0, 200),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', [
                'error' => $e->getMessage(),
                'signature_header' => $sigHeader ? substr($sigHeader, 0, 50) . '...' : 'missing',
                'endpoint_secret_set' => !empty($endpointSecret),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Unexpected error', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }

        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'id' => $event->id
        ]);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'payment_intent.canceled':
                $this->handlePaymentIntentCanceled($event->data->object);
                break;

            case 'charge.refunded':
                $this->handleChargeRefunded($event->data->object);
                break;

            case 'charge.dispute.created':
                $this->handleChargeDisputeCreated($event->data->object);
                break;

            case 'charge.dispute.closed':
                $this->handleChargeDisputeClosed($event->data->object);
                break;

            case 'payment_intent.requires_action':
                $this->handlePaymentIntentRequiresAction($event->data->object);
                break;

            case 'payment_intent.created':
                $this->handlePaymentIntentCreated($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        return response()->json(['received' => true]);
    }

    // Handle successful payment intent
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('=== WEBHOOK: Payment intent succeeded ===', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency ?? 'N/A',
            'status' => $paymentIntent->status ?? 'N/A',
            'customer' => $paymentIntent->customer ?? 'N/A',
            'created' => $paymentIntent->created ?? 'N/A'
        ]);

        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            // Get receipt URL from charge
            $receiptUrl = null;
            $paymentMethodType = null;
            $paymentMethodId = null;
            $stripeFee = null;
            $balanceTransactionId = null;
            
            if ($paymentIntent->latest_charge) {
                try {
                    $charge = Charge::retrieve($paymentIntent->latest_charge);
                    $receiptUrl = $charge->receipt_url ?? null;
                    
                    if (isset($charge->payment_method_details)) {
                        $paymentMethodType = $charge->payment_method_details->type ?? null;
                    }
                    
                    // Retrieve fee from balance transaction
                    if ($charge->balance_transaction) {
                        $balanceTransactionId = is_string($charge->balance_transaction) 
                            ? $charge->balance_transaction 
                            : $charge->balance_transaction->id;
                        
                        try {
                            $balanceTransaction = \Stripe\BalanceTransaction::retrieve($balanceTransactionId);
                            // Fee is in cents, convert to dollars
                            $stripeFee = ($balanceTransaction->fee ?? 0) / 100;
                        } catch (\Exception $e) {
                            Log::warning('Could not retrieve balance transaction', [
                                'balance_transaction_id' => $balanceTransactionId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not retrieve charge details', [
                        'charge_id' => $paymentIntent->latest_charge,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            // Get payment method ID if available
            if (isset($paymentIntent->payment_method)) {
                $paymentMethodId = $paymentIntent->payment_method;
            }
            
            // Get customer ID if available
            $customerId = $paymentIntent->customer ?? null;

            $updateData = [
                'payment_status' => 'paid',
                'payment_confirmed_at' => now(), // Webhook confirms payment
                'stripe_charge_id' => $paymentIntent->latest_charge ?? $order->stripe_charge_id,
                'status' => 'processing',
            ];
            
            // Update fee fields if available
            if ($stripeFee !== null && !$order->stripe_fee) {
                $updateData['stripe_fee'] = round($stripeFee, 2);
                // Calculate net amount: total - stripe_fee
                // Note: total already includes platform_fee (if enabled), so we only subtract Stripe fee
                $netAmount = round($order->total - $stripeFee, 2);
                $updateData['net_amount'] = max(0, $netAmount);
            }
            
            if ($balanceTransactionId && !$order->stripe_balance_transaction_id) {
                $updateData['stripe_balance_transaction_id'] = $balanceTransactionId;
            }
            
            // Update fields only if they're not already set or if we have new data
            if ($receiptUrl && !$order->stripe_receipt_url) {
                $updateData['stripe_receipt_url'] = $receiptUrl;
            }
            
            if ($paymentMethodType && !$order->stripe_payment_method_type) {
                $updateData['stripe_payment_method_type'] = $paymentMethodType;
            }
            
            if ($paymentMethodId && !$order->stripe_payment_method_id) {
                $updateData['stripe_payment_method_id'] = $paymentMethodId;
            }
            
            if ($customerId && !$order->stripe_customer_id) {
                $updateData['stripe_customer_id'] = $customerId;
            }

            $order->update($updateData);

            // Reload order with relationships for email
            $order->load(['items.product', 'billingRegion', 'shippingRegion']);

            // Send order confirmation email after payment is confirmed
            try {
                if (app()->environment('local', 'development')) {
                    Mail::to($order->billing_email)->send(new OrderConfirmationMail($order));
                } else {
                    Mail::to($order->billing_email)->queue(new OrderConfirmationMail($order));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            Log::info('=== WEBHOOK: Order updated after payment success ===', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_status' => $order->fresh()->payment_status,
                'order_status' => $order->fresh()->status,
                'receipt_url' => $receiptUrl ? 'saved' : 'not_available',
                'stripe_fee' => $stripeFee,
                'net_amount' => isset($updateData['net_amount']) ? $updateData['net_amount'] : null,
                'email_sent' => 'queued'
            ]);
        } else {
            Log::info('=== WEBHOOK: Creating order from cached checkout data ===', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency ?? 'N/A',
            ]);
            
            $this->createOrderFromWebhook($paymentIntent);
        }
    }

    // Handle failed payment intent
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        $failureReason = null;
        if (isset($paymentIntent->last_payment_error)) {
            $error = $paymentIntent->last_payment_error;
            $failureReason = ($error->message ?? 'Payment failed') . 
                           (isset($error->code) ? ' (Code: ' . $error->code . ')' : '');
        }

        Log::warning('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
            'last_payment_error' => $paymentIntent->last_payment_error ?? null,
            'failure_reason' => $failureReason,
        ]);

        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'failed',
                'payment_failure_reason' => $failureReason,
                'status' => 'cancelled',
            ]);

            Log::info('Order updated after payment failure', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'failure_reason' => $failureReason,
            ]);
        }
    }

    // Handle canceled payment intent
    protected function handlePaymentIntentCanceled($paymentIntent)
    {
        Log::info('Payment intent canceled', [
            'payment_intent_id' => $paymentIntent->id,
        ]);

        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'cancelled',
                'status' => 'cancelled',
            ]);
        }
    }

    // Handle charge refunded
    protected function handleChargeRefunded($charge)
    {
        Log::info('Charge refunded', [
            'charge_id' => $charge->id,
            'payment_intent_id' => $charge->payment_intent ?? null,
            'amount_refunded' => $charge->amount_refunded ?? 0,
            'refund_reason' => $charge->refunds->data[0]->reason ?? null,
        ]);

        if ($charge->payment_intent) {
            $order = Order::where('stripe_payment_intent_id', $charge->payment_intent)->first();

            if ($order) {
                $refundAmount = ($charge->amount_refunded ?? 0) / 100; // Convert from cents
                $refundReason = null;
                
                // Get refund reason from first refund
                if (isset($charge->refunds->data[0])) {
                    $refund = $charge->refunds->data[0];
                    $refundReason = $refund->reason ?? null;
                }
                
                // Determine if full or partial refund
                $totalAmount = $charge->amount / 100;
                $isFullRefund = $refundAmount >= $totalAmount;
                
                // Note: payment_status enum doesn't include 'partially_refunded'
                // Using 'refunded' for both full and partial, tracking amount in refund_amount
                $order->update([
                    'payment_status' => 'refunded',
                    'refund_amount' => $refundAmount,
                    'refund_reason' => $refundReason,
                    'refunded_at' => now(),
                    'status' => $isFullRefund ? 'cancelled' : $order->status, // Keep status if partial refund
                ]);
                
                Log::info('Order updated after refund', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'refund_amount' => $refundAmount,
                    'is_full_refund' => $isFullRefund,
                ]);
            }
        }
    }

    // Handle charge dispute created
    protected function handleChargeDisputeCreated($dispute)
    {
        Log::warning('Charge dispute created', [
            'dispute_id' => $dispute->id,
            'charge_id' => $dispute->charge ?? null,
            'amount' => $dispute->amount ?? 0,
            'reason' => $dispute->reason ?? null,
            'status' => $dispute->status ?? null,
        ]);

        if ($dispute->charge) {
            $order = Order::where('stripe_charge_id', $dispute->charge)->first();

            if ($order) {
                $disputeAmount = ($dispute->amount ?? 0) / 100; // Convert from cents
                $disputeReason = $dispute->reason ?? 'Unknown';
                
                // Map Stripe dispute reasons to readable format
                $reasonMap = [
                    'bank_cannot_process' => 'Bank cannot process',
                    'check_returned' => 'Check returned',
                    'credit_not_processed' => 'Credit not processed',
                    'customer_initiated' => 'Customer initiated',
                    'debit_not_authorized' => 'Debit not authorized',
                    'duplicate' => 'Duplicate',
                    'fraudulent' => 'Fraudulent',
                    'general' => 'General',
                    'incorrect_account_details' => 'Incorrect account details',
                    'insufficient_funds' => 'Insufficient funds',
                    'product_not_received' => 'Product not received',
                    'product_unacceptable' => 'Product unacceptable',
                    'subscription_canceled' => 'Subscription canceled',
                    'unrecognized' => 'Unrecognized',
                ];

                $disputeReasonLabel = $reasonMap[$disputeReason] ?? ucfirst(str_replace('_', ' ', $disputeReason));

                $order->update([
                    'dispute_status' => 'open',
                    'dispute_reason' => $disputeReasonLabel . ' (Amount: $' . number_format($disputeAmount, 2) . ')',
                ]);

                Log::info('Order updated after dispute created', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'dispute_id' => $dispute->id,
                    'dispute_amount' => $disputeAmount,
                    'dispute_reason' => $disputeReasonLabel,
                ]);
            }
        }
    }

    // Handle charge dispute closed
    protected function handleChargeDisputeClosed($dispute)
    {
        Log::info('Charge dispute closed', [
            'dispute_id' => $dispute->id,
            'charge_id' => $dispute->charge ?? null,
            'status' => $dispute->status ?? null,
            'outcome' => $dispute->outcome->type ?? null,
        ]);

        if ($dispute->charge) {
            $order = Order::where('stripe_charge_id', $dispute->charge)->first();

            if ($order) {
                $disputeStatus = $dispute->status ?? 'closed';
                $outcomeType = $dispute->outcome->type ?? null;
                $outcomeReason = $dispute->outcome->reason ?? null;

                // Determine final dispute status
                $finalStatus = 'closed';
                if ($outcomeType === 'lost' || $outcomeType === 'warning_closed') {
                    $finalStatus = 'lost';
                } elseif ($outcomeType === 'won') {
                    $finalStatus = 'won';
                }

                $disputeReasonUpdate = $order->dispute_reason;
                if ($outcomeReason) {
                    $disputeReasonUpdate .= ' | Outcome: ' . ucfirst(str_replace('_', ' ', $outcomeReason));
                }

                $order->update([
                    'dispute_status' => $finalStatus,
                    'dispute_reason' => $disputeReasonUpdate,
                ]);

                Log::info('Order updated after dispute closed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'dispute_id' => $dispute->id,
                    'final_status' => $finalStatus,
                    'outcome_type' => $outcomeType,
                ]);
            }
        }
    }

    // Handle payment intent requires action (e.g., 3D Secure)
    protected function handlePaymentIntentRequiresAction($paymentIntent)
    {
        Log::info('Payment intent requires action', [
            'payment_intent_id' => $paymentIntent->id,
            'status' => $paymentIntent->status ?? null,
            'next_action' => $paymentIntent->next_action->type ?? null,
        ]);

        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $actionType = $paymentIntent->next_action->type ?? 'unknown';
            $actionNote = 'Payment requires additional action: ' . ucfirst(str_replace('_', ' ', $actionType));

            // Update order notes to track the action requirement
            $existingNotes = $order->notes ?? '';
            $updatedNotes = $existingNotes 
                ? $existingNotes . "\n\n" . date('Y-m-d H:i:s') . ' - ' . $actionNote
                : date('Y-m-d H:i:s') . ' - ' . $actionNote;

            $order->update([
                'notes' => $updatedNotes,
            ]);

            Log::info('Order updated after payment requires action', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'action_type' => $actionType,
            ]);
        }
    }

    // Handle payment intent created
    protected function handlePaymentIntentCreated($paymentIntent)
    {
        Log::info('Payment intent created', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount ?? 0,
            'currency' => $paymentIntent->currency ?? null,
            'status' => $paymentIntent->status ?? null,
        ]);
    }

    /**
     * Create order from cached checkout data (Industry Standard Flow)
     * Called by webhook after payment confirmation
     */
    protected function createOrderFromWebhook($paymentIntent)
    {
        $cacheKey = 'checkout_data_' . $paymentIntent->id;
        $checkoutData = Cache::get($cacheKey);

        if (!$checkoutData) {
            Log::error('=== WEBHOOK: Checkout data not found in cache ===', [
                'payment_intent_id' => $paymentIntent->id,
                'cache_key' => $cacheKey,
                'note' => 'Checkout data may have expired or was never stored'
            ]);
            return;
        }

        $userId = $checkoutData['user_id'] ?? null;
        if (!$userId) {
            Log::error('=== WEBHOOK: User ID missing from checkout data ===', [
                'payment_intent_id' => $paymentIntent->id
            ]);
            return;
        }

        try {
            $shippingService = app(ShippingService::class);
            $priceService = app(PriceCalculationService::class);
            $notificationService = app(NotificationService::class);

            $cartItems = CartItem::with(['product'])
                ->where('user_id', $userId)
                ->get();

            if ($cartItems->isEmpty()) {
                Log::error('=== WEBHOOK: Cart is empty ===', [
                    'payment_intent_id' => $paymentIntent->id,
                    'user_id' => $userId
                ]);
                return;
            }

            $shipping = $checkoutData['shipping'] ?? [];
            $billing = $checkoutData['billing'] ?? [];
            $totals = $checkoutData['totals'] ?? [];
            $appliedCoupon = $checkoutData['applied_coupon'] ?? null;

            $subtotal = $priceService->calculateSubtotal($cartItems);
            $discount = round($appliedCoupon['discount'] ?? 0, 2);
            if ($discount > $subtotal) {
                $discount = round($subtotal, 2);
            }
            $couponCode = $appliedCoupon['code'] ?? null;
            $tax = 0.00;

            $orderAmount = max(0, round($subtotal - $discount, 2));
            $shippingInfo = $shippingService->calculateShippingWithInfo($shipping['region_id'] ?? null, $orderAmount);
            $shippingPrice = round($shippingInfo['shipping_price'], 2);

            $feeCalculation = $priceService->calculateTotalWithAllFees($subtotal, $discount, $shippingPrice);
            $finalTotal = $feeCalculation['final_total'];

            $productIds = $cartItems->pluck('product_id')->unique()->toArray();
            $products = Product::lockForUpdate()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $cartItem) {
                $product = $products->get($cartItem->product_id);
                if (!$product || !$product->status || $product->stock < $cartItem->quantity) {
                    Log::error('=== WEBHOOK: Product validation failed ===', [
                        'payment_intent_id' => $paymentIntent->id,
                        'product_id' => $cartItem->product_id,
                        'product_exists' => !!$product,
                        'product_status' => $product->status ?? null,
                        'stock_available' => $product->stock ?? null,
                        'quantity_requested' => $cartItem->quantity
                    ]);
                    return;
                }
            }

            DB::beginTransaction();

            try {
                $productsForDecrement = Product::lockForUpdate()
                    ->whereIn('id', $productIds)
                    ->get()
                    ->keyBy('id');

                foreach ($cartItems as $cartItem) {
                    $product = $productsForDecrement->get($cartItem->product_id);
                    if ($product->stock < $cartItem->quantity) {
                        throw new \Exception("Insufficient stock for product ID: {$cartItem->product_id}");
                    }
                    $product->decrement('stock', $cartItem->quantity);
                }

                $orderNumber = Order::generateOrderNumber();
                $paymentMethodId = $paymentIntent->payment_method ?? null;
                $customerId = $paymentIntent->customer ?? null;

                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $userId,
                    'session_id' => null,
                    'billing_first_name' => $billing['first_name'] ?? '',
                    'billing_last_name' => $billing['last_name'] ?? '',
                    'billing_email' => $billing['email'] ?? '',
                    'billing_phone' => $billing['phone'] ?? '',
                    'billing_street_address' => $billing['street_address'] ?? '',
                    'billing_city' => $billing['city'] ?? '',
                    'billing_suburb' => $billing['suburb'] ?? '',
                    'billing_region_id' => $billing['region_id'] ?? null,
                    'billing_zip_code' => $billing['zip_code'] ?? '',
                    'billing_country' => 'New Zealand',
                    'shipping_first_name' => $shipping['first_name'] ?? '',
                    'shipping_last_name' => $shipping['last_name'] ?? '',
                    'shipping_email' => $shipping['email'] ?? '',
                    'shipping_phone' => $shipping['phone'] ?? '',
                    'shipping_street_address' => $shipping['street_address'] ?? '',
                    'shipping_city' => $shipping['city'] ?? '',
                    'shipping_suburb' => $shipping['suburb'] ?? '',
                    'shipping_region_id' => $shipping['region_id'] ?? null,
                    'shipping_zip_code' => $shipping['zip_code'] ?? '',
                    'shipping_country' => 'New Zealand',
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'coupon_code' => $couponCode,
                    'tax' => $tax,
                    'shipping' => $shippingPrice,
                    'shipping_price' => $shippingPrice,
                    'total' => $finalTotal,
                    'platform_fee' => $feeCalculation['platform_fee'] ?? 0,
                    'currency' => 'NZD',
                    'payment_method' => 'stripe',
                    'payment_status' => 'paid',
                    'payment_confirmed_at' => now(),
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                    'stripe_customer_id' => $customerId,
                    'stripe_payment_method_id' => $paymentMethodId,
                    'status' => 'processing',
                    'notes' => $checkoutData['notes'] ?? null,
                ]);

                $orderItems = [];
                $now = now();
                foreach ($cartItems as $cartItem) {
                    $orderItems[] = [
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'product_name' => $cartItem->product->name,
                        'product_slug' => $cartItem->product->slug,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->price,
                        'subtotal' => $cartItem->subtotal,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (!empty($orderItems)) {
                    OrderItem::insert($orderItems);
                }

                if ($appliedCoupon) {
                    $coupon = Coupon::find($appliedCoupon['id']);
                    if ($coupon) {
                        $coupon->increment('usage_count');
                    }
                }

                CartItem::where('user_id', $userId)->delete();
                Cache::forget($cacheKey);

                DB::commit();

                $order->load(['items.product', 'billingRegion', 'shippingRegion']);

                try {
                    $notificationService->createOrderNotification($order);
                } catch (\Exception $e) {
                    Log::error('Failed to create admin notification', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }

                try {
                    if (app()->environment('local', 'development')) {
                        Mail::to($order->billing_email)->send(new OrderConfirmationMail($order));
                    } else {
                        Mail::to($order->billing_email)->queue(new OrderConfirmationMail($order));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send order confirmation email', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }

                Log::info('=== WEBHOOK: Order created successfully ===', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_intent_id' => $paymentIntent->id,
                    'user_id' => $userId
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('=== WEBHOOK: Order creation failed ===', [
                    'payment_intent_id' => $paymentIntent->id,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('=== WEBHOOK: Error in createOrderFromWebhook ===', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

