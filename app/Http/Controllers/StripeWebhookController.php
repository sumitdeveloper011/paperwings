<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Helpers\SettingHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Charge;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    // Handle Stripe webhook events
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        // Read Stripe keys from database settings (with .env fallback)
        $endpointSecret = SettingHelper::get('stripe_webhook_secret', config('services.stripe.webhook_secret'));
        $stripeSecret = SettingHelper::get('stripe_secret', config('services.stripe.secret'));

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
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
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

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        return response()->json(['received' => true]);
    }

    // Handle successful payment intent
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
        ]);

        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            // Get receipt URL from charge
            $receiptUrl = null;
            $paymentMethodType = null;
            $paymentMethodId = null;
            
            if ($paymentIntent->latest_charge) {
                try {
                    $charge = Charge::retrieve($paymentIntent->latest_charge);
                    $receiptUrl = $charge->receipt_url ?? null;
                    
                    if (isset($charge->payment_method_details)) {
                        $paymentMethodType = $charge->payment_method_details->type ?? null;
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

            Log::info('Order updated after payment success', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'receipt_url' => $receiptUrl ? 'saved' : 'not_available',
            ]);
        } else {
            Log::warning('Order not found for payment intent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
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
}

