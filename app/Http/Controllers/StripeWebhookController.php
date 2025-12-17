<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        if (!$endpointSecret) {
            Log::error('Stripe webhook secret not configured');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        $event = null;

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
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

    /**
     * Handle successful payment intent
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
        ]);

        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'stripe_charge_id' => $paymentIntent->latest_charge ?? $order->stripe_charge_id,
                'status' => 'processing', // Move to processing when payment confirmed
            ]);

            Log::info('Order updated after payment success', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        } else {
            Log::warning('Order not found for payment intent', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
        }
    }

    /**
     * Handle failed payment intent
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        Log::warning('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
            'last_payment_error' => $paymentIntent->last_payment_error ?? null,
        ]);

        $order = Order::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);

            Log::info('Order updated after payment failure', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        }
    }

    /**
     * Handle canceled payment intent
     */
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

    /**
     * Handle charge refunded
     */
    protected function handleChargeRefunded($charge)
    {
        Log::info('Charge refunded', [
            'charge_id' => $charge->id,
            'payment_intent_id' => $charge->payment_intent ?? null,
        ]);

        if ($charge->payment_intent) {
            $order = Order::where('stripe_payment_intent_id', $charge->payment_intent)->first();

            if ($order) {
                $order->update([
                    'payment_status' => 'refunded',
                    'status' => 'cancelled',
                ]);
            }
        }
    }
}

