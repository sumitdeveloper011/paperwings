<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Region;
use App\Mail\OrderConfirmationMail;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\SettingHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    // Get the identifier for the current cart (user_id only - authentication required)
    private function getCartIdentifier(): array
    {
        return ['user_id' => Auth::id(), 'session_id' => null];
    }

    public function index()
    {
        $title = 'Checkout';
        $cartIdentifier = $this->getCartIdentifier();

        $cartItems = CartItem::with(['product' => function($query) {
                $query->select('id', 'name', 'slug', 'total_price', 'discount_price', 'barcode');
            }])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add items to your cart before checkout.');
        }

        $productIds = $cartItems->pluck('product_id')->unique();
        if ($productIds->isNotEmpty()) {
            $images = \App\Services\ProductImageService::getFirstImagesForProducts($productIds);

            $cartItems->each(function($item) use ($images) {
                if ($item->product) {
                    $image = $images->get($item->product_id);
                    $item->product->setAttribute('main_image',
                        $image ? $image->image_url : asset('assets/images/placeholder.jpg')
                    );
                }
            });
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->subtotal;
        });

        $tax = 0.00;

        $appliedCoupon = Session::get('applied_coupon');
        $discount = 0;
        if ($appliedCoupon) {
            $discount = $appliedCoupon['discount'] ?? 0;
        }

        // Get user data if logged in
        $user = Auth::user();
        $billingAddress = null;
        $shippingAddress = null;
        $regions = Region::where('status', 1)->orderBy('name')->get();

        if ($user) {
            $billingAddress = $user->defaultBillingAddress;
            $shippingAddress = $user->defaultShippingAddress;
        }

        $shippingRegionId = $shippingAddress ? $shippingAddress->region_id : ($billingAddress ? $billingAddress->region_id : null);
        $orderAmount = $subtotal - $discount;
        $shippingInfo = $this->shippingService->calculateShippingWithInfo($shippingRegionId, $orderAmount);
        $shipping = $shippingInfo['shipping_price'];
        $shippingPrice = $shippingInfo['shipping_price'];

        $total = $subtotal - $discount + $tax + $shipping;

        // Read Stripe publishable key from database settings
        $settings = SettingHelper::all();
        $stripePublishableKey = $settings['stripe_key'] ?? config('services.stripe.key');

        return view('frontend.checkout.checkout', compact(
            'title',
            'cartItems',
            'subtotal',
            'tax',
            'shipping',
            'discount',
            'total',
            'appliedCoupon',
            'user',
            'billingAddress',
            'shippingAddress',
            'regions',
            'stripePublishableKey'
        ));
    }

    // Apply coupon code
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->code));

        // Get cart items to calculate subtotal (authentication required)
        $cartItems = CartItem::with(['product'])
            ->where('user_id', Auth::id())
            ->get();

        $subtotal = $cartItems->sum(function($item) {
            return $item->subtotal;
        });

        // Find coupon
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid coupon code.'
                ], 404);
            }
            return redirect()->route('checkout.index')
                ->with('error', 'Invalid coupon code.');
        }

        // Validate coupon
        if (!$coupon->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon is not active.'
                ], 400);
            }
            return redirect()->route('checkout.index')
                ->with('error', 'This coupon is not active.');
        }

        // Check if coupon is not yet active (start date)
        if (now()->lessThan($coupon->start_date)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon is not yet active. Valid from ' . $coupon->start_date->format('M d, Y') . '.'
                ], 400);
            }
            return redirect()->route('checkout.index')
                ->with('error', 'This coupon is not yet active. Valid from ' . $coupon->start_date->format('M d, Y') . '.');
        }

        if ($coupon->isExpired()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon has expired.'
                ], 400);
            }
            return redirect()->route('checkout.index')
                ->with('error', 'This coupon has expired.');
        }

        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon has reached its usage limit.'
                ], 400);
            }
            return redirect()->route('checkout.index')
                ->with('error', 'This coupon has reached its usage limit.');
        }

        // Check per user usage limit
        if ($coupon->usage_limit_per_user) {
            $userCouponUsage = Order::where('user_id', Auth::id())
                ->where('coupon_code', $coupon->code)
                ->where('payment_status', 'paid')
                ->count();

            if ($userCouponUsage >= $coupon->usage_limit_per_user) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have reached the maximum usage limit for this coupon. You can use this coupon ' . $coupon->usage_limit_per_user . ' time(s) only.'
                    ], 400);
                }
                return redirect()->route('checkout.index')
                    ->with('error', 'You have reached the maximum usage limit for this coupon. You can use this coupon ' . $coupon->usage_limit_per_user . ' time(s) only.');
            }
        }

        // Check minimum amount
        if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minimum order amount of $' . number_format($coupon->minimum_amount, 2) . ' required for this coupon.'
                ], 400);
            }
            return redirect()->route('checkout.index')
                ->with('error', 'Minimum order amount of $' . number_format($coupon->minimum_amount, 2) . ' required for this coupon.');
        }

        // Calculate discount
        $discount = 0;
        if ($coupon->type === 'percentage') {
            $discount = ($subtotal * $coupon->value) / 100;
            // Apply maximum discount if set
            if ($coupon->maximum_discount && $discount > $coupon->maximum_discount) {
                $discount = $coupon->maximum_discount;
            }
        } else {
            // Fixed discount
            $discount = $coupon->value;
            // Don't allow discount to exceed subtotal
            if ($discount > $subtotal) {
                $discount = $subtotal;
            }
        }

        $discount = round($discount, 2);
        $total = $subtotal - $discount;

        // Store coupon in session
        Session::put('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount
        ]);

        // Handle AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'coupon' => [
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'discount' => $discount
                ],
                'discount' => $discount,
                'total' => $total
            ]);
        }

        // Regular form submission - redirect back
        return redirect()->route('checkout.index')
            ->with('success', 'Coupon applied successfully!');
    }

    // Remove coupon
    public function removeCoupon(Request $request)
    {
        Session::forget('applied_coupon');

        // Handle AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed successfully.'
            ]);
        }

        // Regular form submission - redirect back
        return redirect()->route('checkout.index')
            ->with('success', 'Coupon removed successfully.');
    }

    // Calculate shipping price
    public function calculateShipping(Request $request): JsonResponse
    {
        $request->validate([
            'region_id' => 'required|exists:regions,id',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $orderAmount = $request->subtotal - ($request->discount ?? 0);
        $shippingInfo = $this->shippingService->calculateShippingWithInfo($request->region_id, $orderAmount);

        return response()->json([
            'success' => true,
            'shipping' => $shippingInfo['shipping_price'],
            'shipping_price' => $shippingInfo['shipping_price'],
            'free_shipping_minimum' => $shippingInfo['free_shipping_minimum'],
            'is_free_shipping' => $shippingInfo['is_free_shipping'],
            'message' => $shippingInfo['is_free_shipping']
                ? 'Free shipping applied!'
                : 'Shipping calculated successfully.'
        ]);
    }

    // Create Stripe Payment Intent
    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.50'
        ]);

        // Read Stripe secret from database settings
        $settings = SettingHelper::all();
        $stripeSecret = $settings['stripe_secret'] ?? config('services.stripe.secret');

        if (!$stripeSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured. Please contact support.'
            ], 500);
        }

        try {
            Stripe::setApiKey($stripeSecret);

            $paymentIntent = PaymentIntent::create([
                'amount' => (int)round($request->amount * 100), // Convert to cents
                'currency' => 'nzd', // Stripe accepts lowercase currency codes
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            if (!$paymentIntent->client_secret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment intent. Please try again.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'clientSecret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe PaymentIntent creation failed', [
                'error' => $e->getMessage(),
                'amount' => $request->amount,
                'stripe_error_code' => $e->getStripeCode(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('PaymentIntent creation exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    // Process order and payment
    public function processOrder(Request $request)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:255',
            'billing_street_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_region_id' => 'required|exists:regions,id',
            'billing_zip_code' => 'required|string|max:255',
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:255',
            'shipping_street_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_region_id' => 'required|exists:regions,id',
            'shipping_zip_code' => 'required|string|max:255',
            'payment_intent_id' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Get cart items (authentication required)
        $cartItems = CartItem::with(['product'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.'
            ], 400);
        }

        // Verify payment with Stripe - Read from database settings
        $settings = SettingHelper::all();
        $stripeSecret = $settings['stripe_secret'] ?? config('services.stripe.secret');

        if (!$stripeSecret) {
            Log::error('Stripe secret key not configured');
            return response()->json([
                'success' => false,
                'message' => 'Stripe is not configured. Please contact support.'
            ], 500);
        }

        try {
            Stripe::setApiKey($stripeSecret);
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            // Only accept succeeded status - webhook will handle status updates
            if ($paymentIntent->status !== 'succeeded') {
                Log::warning('Stripe payment status check failed', [
                    'payment_intent_id' => $request->payment_intent_id,
                    'status' => $paymentIntent->status,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed. Status: ' . $paymentIntent->status . '. Please try again.'
                ], 400);
            }

            // Calculate totals
            $subtotal = round($cartItems->sum(function($item) {
                return $item->subtotal;
            }), 2);

            $appliedCoupon = Session::get('applied_coupon');
            $discount = round($appliedCoupon['discount'] ?? 0, 2);
            $couponCode = $appliedCoupon['code'] ?? null;
            $tax = 0.00;

            // Calculate shipping based on shipping region
            $orderAmount = round($subtotal - $discount, 2);
            $shippingInfo = $this->shippingService->calculateShippingWithInfo($request->shipping_region_id, $orderAmount);
            $shipping = round($shippingInfo['shipping_price'], 2);
            $shippingPrice = round($shippingInfo['shipping_price'], 2);

            // Round total to avoid floating point precision issues
            $total = round($subtotal - $discount + $tax + $shipping, 2);

            // Verify total matches payment amount
            $paymentAmount = $paymentIntent->amount / 100; // Convert from cents
            $difference = abs($total - $paymentAmount);

            // Log the mismatch for debugging
            if ($difference > 0.01) {
                Log::warning('Payment amount mismatch detected', [
                    'calculated_total' => $total,
                    'payment_amount' => $paymentAmount,
                    'difference' => $difference,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'shipping_region_id' => $request->shipping_region_id,
                    'payment_intent_id' => $request->payment_intent_id,
                ]);
            }

            // Allow small rounding differences (up to 0.05 cents) due to floating point precision
            if ($difference > 0.05) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount mismatch. Please refresh and try again.',
                    'debug' => app()->environment('local') ? [
                        'calculated_total' => round($total, 2),
                        'payment_amount' => round($paymentAmount, 2),
                        'difference' => round($difference, 2)
                    ] : null
                ], 400);
            }

            // Create order
            DB::beginTransaction();

            try {
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => Auth::id(),
                    'session_id' => null, // No guest orders - authentication required
                    'billing_first_name' => $request->billing_first_name,
                    'billing_last_name' => $request->billing_last_name,
                    'billing_email' => $request->billing_email,
                    'billing_phone' => $request->billing_phone,
                    'billing_street_address' => $request->billing_street_address,
                    'billing_city' => $request->billing_city,
                    'billing_suburb' => $request->billing_suburb,
                    'billing_region_id' => $request->billing_region_id,
                    'billing_zip_code' => $request->billing_zip_code,
                    'billing_country' => 'New Zealand',
                    'shipping_first_name' => $request->shipping_first_name,
                    'shipping_last_name' => $request->shipping_last_name,
                    'shipping_email' => $request->shipping_email,
                    'shipping_phone' => $request->shipping_phone,
                    'shipping_street_address' => $request->shipping_street_address,
                    'shipping_city' => $request->shipping_city,
                    'shipping_suburb' => $request->shipping_suburb,
                    'shipping_region_id' => $request->shipping_region_id,
                    'shipping_zip_code' => $request->shipping_zip_code,
                    'shipping_country' => 'New Zealand',
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'coupon_code' => $couponCode,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'shipping_price' => $shippingPrice,
                    'total' => $total,
                    'payment_method' => 'stripe',
                    'payment_status' => 'paid', // Will be confirmed by webhook
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                    'status' => 'processing', // Start as processing when payment succeeded
                    'notes' => $request->notes ?? null,
                ]);

                // Bulk insert order items for better performance
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

                // Single bulk insert instead of multiple individual inserts
                if (!empty($orderItems)) {
                    OrderItem::insert($orderItems);
                }

                // Update coupon usage if applied
                if ($appliedCoupon) {
                    $coupon = Coupon::find($appliedCoupon['id']);
                    if ($coupon) {
                        $coupon->increment('usage_count');
                    }
                }

                // Clear cart (authentication required)
                CartItem::where('user_id', Auth::id())->delete();

                // Clear coupon from session
                Session::forget('applied_coupon');

                DB::commit();

                // Load order with relationships for email
                $order->load(['items.product', 'billingRegion', 'shippingRegion']);

                // Send order confirmation email
                try {
                    // Queue email for faster response (OrderConfirmationMail already implements ShouldQueue)
                    Mail::to($order->billing_email)->queue(new OrderConfirmationMail($order));
                } catch (\Exception $e) {
                    // Log email error but don't fail the order
                    Log::error('Failed to send order confirmation email: ' . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'redirect_url' => route('checkout.success', ['order' => $order->order_number])
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create order: ' . $e->getMessage()
                ], 500);
            }

        } catch (ApiErrorException $e) {
            Log::error('Stripe PaymentIntent verification failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $request->payment_intent_id ?? 'missing',
                'stripe_error_code' => $e->getStripeCode(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Payment verification exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }

    // Show order success page (only show success if payment is actually confirmed)
    public function success(Request $request, $orderNumber)
    {
        try {
            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            // Verify payment status before showing success
            if ($order->payment_status !== 'paid') {
                return redirect()->route('checkout.index')
                    ->with('error', 'Payment is still being processed. Please check your order status in your account.');
            }

            // Double-check with Stripe if payment intent exists
            if ($order->stripe_payment_intent_id) {
                try {
                    // Read Stripe secret from database settings
                    $settings = SettingHelper::all();
                    $stripeSecret = $settings['stripe_secret'] ?? config('services.stripe.secret');
                    if ($stripeSecret) {
                        Stripe::setApiKey($stripeSecret);
                        $paymentIntent = PaymentIntent::retrieve($order->stripe_payment_intent_id);

                        if ($paymentIntent->status !== 'succeeded') {
                            return redirect()->route('checkout.index')
                                ->with('error', 'Payment is still being processed. Please check your order status in your account.');
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error verifying payment on success page', [
                        'order_number' => $orderNumber,
                        'error' => $e->getMessage()
                    ]);
                    // Continue to show success page even if Stripe verification fails
                    // The payment_status check above is sufficient
                }
            }

            // Load order relationships for e-commerce tracking (with error handling)
            try {
                $order->load(['items.product.category']);
            } catch (\Exception $e) {
                Log::warning('Error loading order relationships for success page', [
                    'order_number' => $orderNumber,
                    'error' => $e->getMessage()
                ]);
                // Try to load without category relationship
                try {
                    $order->load(['items.product']);
                } catch (\Exception $e2) {
                    Log::warning('Error loading order items for success page', [
                        'order_number' => $orderNumber,
                        'error' => $e2->getMessage()
                    ]);
                }
            }

            return view('frontend.checkout.success', [
                'title' => 'Order Confirmation',
                'order' => $order
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error('Order not found for success page', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('checkout.index')
                ->with('error', 'Order not found. Please check your order number and try again.');
        } catch (\Exception $e) {
            Log::error('Error loading checkout success page', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('checkout.index')
                ->with('error', 'An error occurred while loading the order confirmation page. Please try again or contact support.');
        }
    }
}
