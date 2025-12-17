# Stripe Payment Flow - Best Practices Implementation

## Overview
This document explains the secure Stripe payment flow implemented in the application, including webhook handling and proper status management.

## Payment Flow

### 1. **Payment Intent Creation**
- User fills checkout form
- Frontend calls `/checkout/create-payment-intent`
- Server creates Stripe PaymentIntent
- Returns `client_secret` to frontend

### 2. **Payment Confirmation (Frontend)**
- Stripe Elements handles payment collection
- User submits payment
- Frontend calls `stripe.confirmPayment()`
- Payment is processed by Stripe

### 3. **Order Creation (Backend)**
- Frontend sends order data with `payment_intent_id`
- Server verifies payment status with Stripe API
- **Only creates order if payment status is `succeeded`**
- Order is created with `payment_status = 'paid'` and `status = 'processing'`

### 4. **Webhook Confirmation (Backend)**
- Stripe sends webhook event to `/stripe/webhook`
- Webhook verifies signature
- Updates order status based on event:
  - `payment_intent.succeeded` → Confirms payment, sets status to `processing`
  - `payment_intent.payment_failed` → Sets `payment_status = 'failed'`, `status = 'cancelled'`
  - `payment_intent.canceled` → Sets `payment_status = 'cancelled'`
  - `charge.refunded` → Sets `payment_status = 'refunded'`

### 5. **Success Page**
- User is redirected to success page
- **Success page verifies payment status before showing**
- If payment not confirmed, redirects back with error message

## Security Features

### 1. **Payment Verification**
- Server-side verification of payment status
- Only accepts `succeeded` status for order creation
- Rejects `processing` or other statuses

### 2. **Webhook Signature Verification**
- All webhooks verify Stripe signature
- Prevents unauthorized webhook calls
- Uses `STRIPE_WEBHOOK_SECRET` from environment

### 3. **Amount Verification**
- Verifies payment amount matches order total
- Prevents amount manipulation

### 4. **User Verification**
- Success page verifies payment before showing
- Double-checks with Stripe API if needed

## Setup Instructions

### 1. **Environment Variables**
Add to your `.env` file:
```env
STRIPE_KEY=pk_test_... (or pk_live_...)
STRIPE_SECRET=sk_test_... (or sk_live_...)
STRIPE_WEBHOOK_SECRET=whsec_... (from Stripe Dashboard)
```

### 2. **Stripe Dashboard Setup**
1. Go to Stripe Dashboard → Developers → Webhooks
2. Add endpoint: `https://yourdomain.com/stripe/webhook`
3. Select events to listen for:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `payment_intent.canceled`
   - `charge.refunded`
4. Copy the webhook signing secret to `STRIPE_WEBHOOK_SECRET`

### 3. **Testing**
- Use Stripe test cards: `4242 4242 4242 4242`
- Test webhooks using Stripe CLI: `stripe listen --forward-to localhost:8000/stripe/webhook`

## Order Status Flow

```
1. Payment Intent Created → No order yet
2. Payment Succeeded → Order created with status 'processing', payment_status 'paid'
3. Webhook Confirmed → Status remains 'processing' (or updated if needed)
4. Admin Processing → Status changes to 'shipped'
5. Delivered → Status changes to 'delivered'
```

## Payment Status Flow

```
1. Payment Intent Created → No payment status
2. Payment Succeeded → payment_status = 'paid'
3. Webhook Confirmed → payment_status = 'paid' (confirmed)
4. If Failed → payment_status = 'failed', status = 'cancelled'
5. If Refunded → payment_status = 'refunded', status = 'cancelled'
```

## Error Handling

### Payment Not Completed
- User sees error message
- Order is NOT created
- Payment intent remains in Stripe
- User can retry payment

### Payment Failed After Order Creation
- Webhook updates order status
- Order is cancelled
- User is notified (can be implemented)

### Webhook Not Received
- Order remains with `payment_status = 'paid'`
- Can be manually verified by admin
- Success page double-checks with Stripe API

## Best Practices Implemented

✅ Server-side payment verification
✅ Webhook signature verification
✅ Only create order on successful payment
✅ Double-check payment on success page
✅ Proper error handling and logging
✅ Status tracking for both order and payment
✅ Amount verification
✅ User verification

## Monitoring

Check logs for:
- `storage/logs/laravel.log` - All payment and webhook events
- Stripe Dashboard → Events - All webhook events
- Order status changes in database

