# Order Status Explanation

## What is "Pending" Status?

**"Pending"** status means the order is waiting for action or confirmation. It's typically used when:

1. **Manual Admin Assignment**: An admin manually sets an order to "pending" status in the backend
2. **Payment Issues**: When payment verification fails or payment is still being processed
3. **Order Review**: When an order needs manual review before processing
4. **Stock Issues**: When items need to be verified or restocked before processing

## Current Order Flow in This System

### When Order is Created:
- **Status**: `processing` (automatically set when payment succeeds)
- **Payment Status**: `paid` (when Stripe payment is confirmed)
- **Location**: `CheckoutController::processOrder()` (line 1052)

### Status Options Available:
1. **pending** - Order is waiting for action
2. **processing** - Order is being prepared (default when payment succeeds)
3. **shipped** - Order has been shipped (requires tracking info)
4. **delivered** - Order has been delivered to customer
5. **cancelled** - Order has been cancelled

## How Orders Get "Pending" Status

### Method 1: Manual Admin Update
- Admin goes to **Order Details** page (`/admin/orders/{order}`)
- Changes order status dropdown to "Pending"
- Clicks save/updates status
- **Location**: `OrderController::updateStatus()` (line 108)

### Method 2: Payment Failure
- If payment fails during checkout, order might be created with `payment_status = 'failed'`
- Admin can then manually set status to "pending" for review

### Method 3: Manual Order Creation (if implemented)
- If admin creates orders manually, they might start as "pending"

## Payment Status vs Order Status

### Payment Status:
- **paid** - Payment confirmed
- **pending** - Payment being processed
- **failed** - Payment failed
- **refunded** - Payment refunded

### Order Status:
- **pending** - Order waiting for action
- **processing** - Order being prepared
- **shipped** - Order shipped
- **delivered** - Order delivered
- **cancelled** - Order cancelled

## Important Notes

1. **Default Status**: New orders are created with `status = 'processing'` when payment succeeds
2. **Pending is Manual**: "Pending" status is typically set manually by admins, not automatically
3. **Statistics**: The dashboard shows count of orders with `status = 'pending'` (line 59 in OrderController)
4. **Email Notifications**: Status changes trigger email notifications to customers

## Recommendation

If you want orders to start as "pending" instead of "processing", you would need to:
1. Change line 1052 in `CheckoutController::processOrder()` from `'status' => 'processing'` to `'status' => 'pending'`
2. Or implement a business rule that sets pending based on certain conditions
