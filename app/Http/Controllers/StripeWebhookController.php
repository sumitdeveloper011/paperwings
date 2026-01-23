<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

            Log::info('Order updated after payment success', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'receipt_url' => $receiptUrl ? 'saved' : 'not_available',
                'stripe_fee' => $stripeFee,
                'net_amount' => isset($updateData['net_amount']) ? $updateData['net_amount'] : null,
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

        // Payment intent created is mainly for logging
        // The order is already created in CheckoutController before payment intent
        // This webhook is useful for tracking and analytics
    }
}

