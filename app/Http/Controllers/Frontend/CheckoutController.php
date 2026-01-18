<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreCheckoutRequest;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Region;
use App\Mail\OrderConfirmationMail;
use App\Services\ShippingService;
use App\Services\NZPostAddressService;
use App\Services\CheckoutSessionService;
use App\Services\GoogleAnalyticsService;
use App\Services\NotificationService;
use App\Services\PriceCalculationService;
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
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;


class CheckoutController extends Controller
{
    protected $shippingService;
    protected $nzPostService;
    protected $checkoutSession;
    protected $notificationService;
    protected $priceService;

    public function __construct(
        ShippingService $shippingService,
        NZPostAddressService $nzPostService,
        CheckoutSessionService $checkoutSession,
        NotificationService $notificationService,
        PriceCalculationService $priceService
    ) {
        $this->shippingService = $shippingService;
        $this->nzPostService = $nzPostService;
        $this->checkoutSession = $checkoutSession;
        $this->notificationService = $notificationService;
        $this->priceService = $priceService;
    }

    // Get the identifier for the current cart (user_id only - authentication required)
    private function getCartIdentifier(): array
    {
        return ['user_id' => Auth::id(), 'session_id' => null];
    }

    /**
     * Legacy route - redirect to new details page
     */
    public function index()
    {
        return redirect()->route('checkout.details');
    }

    /**
     * Step 1: Show checkout details form
     */
    public function details()
    {
        $title = 'Checkout';
        $cartItems = CartItem::with(['product' => function($query) {
                $query->select('id', 'name', 'slug', 'total_price', 'discount_price', 'barcode');
            }, 'product.images' => function($query) {
                $query->select('id', 'product_id', 'image')->orderBy('id', 'asc')->limit(1);
            }])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $this->priceService->calculateSubtotal($cartItems);

        $appliedCoupon = Session::get('applied_coupon');
        $discount = (is_array($appliedCoupon) && isset($appliedCoupon['discount'])) ? (float)$appliedCoupon['discount'] : 0;

        $user = Auth::user();
        $billingAddress = null;
        $shippingAddress = null;
        $billingAddresses = collect();
        $shippingAddresses = collect();
        $regions = Region::where('status', 1)->orderBy('name')->get();

        if ($user) {
            $billingAddress = $user->defaultBillingAddress;
            $shippingAddress = $user->defaultShippingAddress;
            
            $billingAddresses = \App\Models\UserAddress::where('user_id', $user->id)
                ->where('type', 'billing')
                ->with('region')
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $shippingAddresses = \App\Models\UserAddress::where('user_id', $user->id)
                ->where('type', 'shipping')
                ->with('region')
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $sessionData = $this->checkoutSession->getCheckoutData();
        
        $shippingRegionId = $shippingAddress ? $shippingAddress->region_id : ($billingAddress ? $billingAddress->region_id : null);
        // Calculate order amount for shipping (intermediate value, acceptable to calculate directly)
        $orderAmount = $subtotal - $discount;
        $shippingInfo = $this->shippingService->calculateShippingWithInfo($shippingRegionId, $orderAmount);
        $shipping = $shippingInfo['shipping_price'];
        // Calculate total using PriceCalculationService
        $total = $this->priceService->calculateTotal($subtotal, $discount, $shipping);

        return view('frontend.checkout.details', compact(
            'title',
            'cartItems',
            'subtotal',
            'shipping',
            'discount',
            'total',
            'appliedCoupon',
            'user',
            'billingAddress',
            'shippingAddress',
            'billingAddresses',
            'shippingAddresses',
            'regions',
            'sessionData'
        ));
    }

    /**
     * Step 1: Store checkout details and redirect to review
     */
    public function storeDetails(Request $request)
    {
        $validated = $request->validate([
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_street_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:255',
            'shipping_suburb' => 'nullable|string|max:255',
            'shipping_region_id' => 'required|exists:regions,id',
            'shipping_zip_code' => 'required|string|max:10',
            'shipping_country' => 'required|string|max:255',
            'billing_different' => 'nullable|boolean',
            'billing_first_name' => 'required_if:billing_different,1|string|max:255',
            'billing_last_name' => 'required_if:billing_different,1|string|max:255',
            'billing_email' => 'required_if:billing_different,1|email|max:255',
            'billing_phone' => 'required_if:billing_different,1|string|max:20',
            'billing_street_address' => 'required_if:billing_different,1|string|max:500',
            'billing_city' => 'required_if:billing_different,1|string|max:255',
            'billing_suburb' => 'nullable|string|max:255',
            'billing_region_id' => 'required_if:billing_different,1|exists:regions,id',
            'billing_zip_code' => 'required_if:billing_different,1|string|max:10',
            'billing_country' => 'required_if:billing_different,1|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $billingDifferent = $request->has('billing_different') && $request->billing_different == '1';

        $checkoutData = [
            'shipping' => [
                'first_name' => $validated['shipping_first_name'],
                'last_name' => $validated['shipping_last_name'],
                'email' => $validated['shipping_email'],
                'phone' => $validated['shipping_phone'],
                'street_address' => $validated['shipping_street_address'],
                'city' => $validated['shipping_city'],
                'suburb' => $validated['shipping_suburb'] ?? '',
                'region_id' => $validated['shipping_region_id'],
                'zip_code' => $validated['shipping_zip_code'],
                'country' => $validated['shipping_country'],
            ],
            'billing' => $billingDifferent ? [
                'first_name' => $validated['billing_first_name'],
                'last_name' => $validated['billing_last_name'],
                'email' => $validated['billing_email'],
                'phone' => $validated['billing_phone'],
                'street_address' => $validated['billing_street_address'],
                'city' => $validated['billing_city'],
                'suburb' => $validated['billing_suburb'] ?? '',
                'region_id' => $validated['billing_region_id'],
                'zip_code' => $validated['billing_zip_code'],
                'country' => $validated['billing_country'],
            ] : [],
            'notes' => $validated['notes'] ?? '',
            'billing_different' => $billingDifferent,
        ];

        $this->checkoutSession->storeDetails($checkoutData);

        return redirect()->route('checkout.review');
    }

    /**
     * Step 2: Show review page
     */
    public function review()
    {
        if (!$this->checkoutSession->hasRequiredData()) {
            return redirect()->route('checkout.details')->with('error', 'Please complete the checkout details first.');
        }

        $title = 'Review Order';

        $cartItems = CartItem::with(['product' => function($query) {
                $query->select('id', 'name', 'slug', 'total_price', 'discount_price', 'barcode');
            }, 'product.images' => function($query) {
                $query->select('id', 'product_id', 'image')->orderBy('id', 'asc')->limit(1);
            }])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $this->priceService->calculateSubtotal($cartItems);

        $appliedCoupon = Session::get('applied_coupon');
        
        // Extract discount - ensure it's always a float
        $discount = 0.0;
        if (is_array($appliedCoupon) && isset($appliedCoupon['discount'])) {
            $discountValue = $appliedCoupon['discount'];
            
            // Handle if discount is an array (shouldn't happen, but be safe)
            if (is_array($discountValue)) {
                $discount = isset($discountValue['value']) ? (float)$discountValue['value'] : 0.0;
            } elseif (is_numeric($discountValue)) {
                $discount = (float)$discountValue;
            } else {
                $discount = 0.0;
            }
        }
        
        // Final safety check - ensure it's a float
        if (!is_numeric($discount) || is_array($discount)) {
            $discount = 0.0;
        }
        
        $discount = (float)$discount;

        $sessionData = $this->checkoutSession->getCheckoutData();
        // Ensure region_id is integer, not string
        $shippingRegionId = isset($sessionData['shipping']['region_id']) ? (int)$sessionData['shipping']['region_id'] : null;
        $orderAmount = $subtotal - $discount;
        
        $shippingInfo = $this->shippingService->calculateShippingWithInfo($shippingRegionId, $orderAmount);
        
        // Ensure shipping is always a float
        $shipping = isset($shippingInfo['shipping_price']) ? (float)$shippingInfo['shipping_price'] : 0.0;
        if (!is_numeric($shipping) || is_array($shipping)) {
            Log::warning('Shipping is not numeric in review method', [
                'shipping' => $shipping,
                'shippingInfo' => $shippingInfo
            ]);
            $shipping = 0.0;
        }
        $shipping = (float)$shipping;
        
        if (app()->environment('local')) {
            Log::info('Review method - Final shipping value', [
                'shipping' => $shipping
            ]);
        }
        
        // Calculate total using PriceCalculationService
        $total = $this->priceService->calculateTotal($subtotal, $discount, $shipping);

        $regions = Region::where('status', 1)->orderBy('name')->get()->keyBy('id');

        $this->checkoutSession->storeTotals([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => $total,
        ]);

        return view('frontend.checkout.review', compact(
            'title',
            'cartItems',
            'subtotal',
            'shipping',
            'discount',
            'total',
            'appliedCoupon',
            'sessionData',
            'regions'
        ));
    }

    /**
     * Step 2: Confirm review and redirect to payment
     */
    public function confirmReview(Request $request)
    {
        if (!$this->checkoutSession->hasRequiredData()) {
            return redirect()->route('checkout.details')->with('error', 'Please complete the checkout details first.');
        }

        return redirect()->route('checkout.payment');
    }

    /**
     * Step 3: Show payment page
     */
    public function payment()
    {
        if (!$this->checkoutSession->hasRequiredData()) {
            return redirect()->route('checkout.details')->with('error', 'Please complete the checkout details first.');
        }

        $title = 'Payment';

        $cartItems = CartItem::with(['product' => function($query) {
                $query->select('id', 'name', 'slug', 'total_price', 'discount_price', 'barcode');
            }, 'product.images' => function($query) {
                $query->select('id', 'product_id', 'image')->orderBy('id', 'asc')->limit(1);
            }])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $totals = $this->checkoutSession->getTotals();
        if (empty($totals)) {
            return redirect()->route('checkout.review');
        }

        $appliedCoupon = Session::get('applied_coupon');
        $sessionData = $this->checkoutSession->getCheckoutData();

        $stripePublishableKey = SettingHelper::get('stripe_key', config('services.stripe.key'));

        return view('frontend.checkout.payment', compact(
            'title',
            'cartItems',
            'totals',
            'appliedCoupon',
            'sessionData',
            'stripePublishableKey'
        ));
    }

    /**
     * Step 3: Process payment
     */
    public function processPayment(Request $request)
    {
        if (!$this->checkoutSession->hasRequiredData()) {
            return redirect()->route('checkout.details')->with('error', 'Please complete the checkout details first.');
        }

        $sessionData = $this->checkoutSession->getCheckoutData();
        $totals = $this->checkoutSession->getTotals();

        $request->merge([
            'shipping_first_name' => $sessionData['shipping']['first_name'],
            'shipping_last_name' => $sessionData['shipping']['last_name'],
            'shipping_email' => $sessionData['shipping']['email'],
            'shipping_phone' => $sessionData['shipping']['phone'],
            'shipping_street_address' => $sessionData['shipping']['street_address'],
            'shipping_city' => $sessionData['shipping']['city'],
            'shipping_suburb' => $sessionData['shipping']['suburb'] ?? '',
            'shipping_region_id' => $sessionData['shipping']['region_id'],
            'shipping_zip_code' => $sessionData['shipping']['zip_code'],
            'shipping_country' => $sessionData['shipping']['country'],
            'billing_first_name' => $sessionData['billing_different'] ? $sessionData['billing']['first_name'] : $sessionData['shipping']['first_name'],
            'billing_last_name' => $sessionData['billing_different'] ? $sessionData['billing']['last_name'] : $sessionData['shipping']['last_name'],
            'billing_email' => $sessionData['billing_different'] ? $sessionData['billing']['email'] : $sessionData['shipping']['email'],
            'billing_phone' => $sessionData['billing_different'] ? $sessionData['billing']['phone'] : $sessionData['shipping']['phone'],
            'billing_street_address' => $sessionData['billing_different'] ? $sessionData['billing']['street_address'] : $sessionData['shipping']['street_address'],
            'billing_city' => $sessionData['billing_different'] ? $sessionData['billing']['city'] : $sessionData['shipping']['city'],
            'billing_suburb' => $sessionData['billing_different'] ? ($sessionData['billing']['suburb'] ?? '') : ($sessionData['shipping']['suburb'] ?? ''),
            'billing_region_id' => $sessionData['billing_different'] ? $sessionData['billing']['region_id'] : $sessionData['shipping']['region_id'],
            'billing_zip_code' => $sessionData['billing_different'] ? $sessionData['billing']['zip_code'] : $sessionData['shipping']['zip_code'],
            'billing_country' => $sessionData['billing_different'] ? $sessionData['billing']['country'] : $sessionData['shipping']['country'],
            'notes' => $sessionData['notes'] ?? '',
        ]);

        $storeRequest = new StoreCheckoutRequest();
        $storeRequest->merge($request->all());
        
        $result = $this->processOrder($storeRequest);
        
        if ($result instanceof \Illuminate\Http\RedirectResponse && $result->getTargetUrl() !== route('checkout.payment')) {
            $this->checkoutSession->clear();
        }
        
        return $result;
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

        $subtotal = $this->priceService->calculateSubtotal($cartItems);

        // Find coupon
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return $this->jsonError('Invalid coupon code.', 'COUPON_NOT_FOUND', null, 404);
            }
            return redirect()->route('checkout.details')
                ->with('error', 'Invalid coupon code.');
        }

        // Validate coupon
        if (!$coupon->isActive()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return $this->jsonError('This coupon is not active.', 'COUPON_INACTIVE', null, 400);
            }
            return redirect()->route('checkout.details')
                ->with('error', 'This coupon is not active.');
        }

        // Check if coupon is not yet active (start date)
        if (now()->lessThan($coupon->start_date)) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return $this->jsonError('This coupon is not yet active. Valid from ' . $coupon->start_date->format('M d, Y') . '.', 'COUPON_NOT_STARTED', null, 400);
            }
            return redirect()->route('checkout.details')
                ->with('error', 'This coupon is not yet active. Valid from ' . $coupon->start_date->format('M d, Y') . '.');
        }

        if ($coupon->isExpired()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return $this->jsonError('This coupon has expired.', 'COUPON_EXPIRED', null, 400);
            }
            return redirect()->route('checkout.details')
                ->with('error', 'This coupon has expired.');
        }

        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return $this->jsonError('This coupon has reached its usage limit.', 'COUPON_USAGE_LIMIT_REACHED', null, 400);
            }
            return redirect()->route('checkout.details')
                ->with('error', 'This coupon has reached its usage limit.');
        }

        // Check per user usage limit
        if ($coupon->usage_limit_per_user) {
            $userCouponUsage = Order::where('user_id', Auth::id())
                ->where('coupon_code', $coupon->code)
                ->where('payment_status', 'paid')
                ->count();

            if ($userCouponUsage >= $coupon->usage_limit_per_user) {
                if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                    return $this->jsonError('You have reached the maximum usage limit for this coupon. You can use this coupon ' . $coupon->usage_limit_per_user . ' time(s) only.', 'COUPON_USER_LIMIT_REACHED', null, 400);
                }
                return redirect()->route('checkout.details')
                    ->with('error', 'You have reached the maximum usage limit for this coupon. You can use this coupon ' . $coupon->usage_limit_per_user . ' time(s) only.');
            }
        }

        // Check minimum amount
        if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return $this->jsonError('Minimum order amount of $' . number_format($coupon->minimum_amount, 2) . ' required for this coupon.', 'COUPON_MINIMUM_AMOUNT', null, 400);
            }
            return redirect()->route('checkout.details')
                ->with('error', 'Minimum order amount of $' . number_format($coupon->minimum_amount, 2) . ' required for this coupon.');
        }

        // Calculate discount using PriceCalculationService
        $discountType = $coupon->type === 'percentage' ? 'percentage' : 'fixed';
        $discount = $this->priceService->calculateDiscountAmount(
            $subtotal,
            $discountType,
            $coupon->value
        );
        
        // Apply maximum discount if set (for percentage coupons)
        if ($coupon->type === 'percentage' && $coupon->maximum_discount && $discount > $coupon->maximum_discount) {
            $discount = round($coupon->maximum_discount, 2);
        }
        
        // Don't allow discount to exceed subtotal (for fixed coupons)
        if ($coupon->type === 'fixed' && $discount > $subtotal) {
            $discount = round($subtotal, 2);
        }
        
        $discount = round($discount, 2);
        // Calculate total using PriceCalculationService (no shipping in coupon calculation)
        $total = $this->priceService->calculateTotal($subtotal, $discount, 0);

        // Store coupon in session
        Session::put('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount
        ]);

        try {
            $analyticsService = app(GoogleAnalyticsService::class);
            $analyticsService->trackEvent('apply_promotion', [
                'promotion_id' => $coupon->code,
                'promotion_name' => $coupon->name,
                'value' => $discount,
                'currency' => 'NZD'
            ]);
        } catch (\Exception $e) {
            Log::warning('Analytics tracking failed for coupon', [
                'error' => $e->getMessage()
            ]);
        }

        // Handle AJAX/JSON requests
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return $this->jsonSuccess('Coupon applied successfully!', [
                'coupon' => [
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'discount' => $discount
                ],
                'discount' => $discount,
                'total' => $total
            ]);
        }

        // Regular form submission - redirect back to details page
        return redirect()->route('checkout.details')
            ->with('success', 'Coupon applied successfully!');
    }

    // Remove coupon
    public function removeCoupon(Request $request)
    {
        try {
            Session::forget('applied_coupon');

            // Handle AJAX/JSON requests
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return $this->jsonSuccess('Coupon removed successfully.');
            }

            // Regular form submission - redirect back to details page
            return redirect()->route('checkout.details')
                ->with('success', 'Coupon removed successfully.');
        } catch (\Exception $e) {
            Log::error('Remove coupon error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            // Handle AJAX/JSON requests
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return $this->jsonError('An error occurred while removing the coupon. Please try again.', 'COUPON_REMOVE_ERROR', null, 500);
            }

            // Regular form submission
            return redirect()->route('checkout.details')
                ->with('error', 'An error occurred while removing the coupon. Please try again.');
        }
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

        return $this->jsonSuccess('Shipping calculated.', [
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

        // Read Stripe secret from database settings (with .env fallback)
        $stripeSecret = SettingHelper::get('stripe_secret', config('services.stripe.secret'));

        if (!$stripeSecret) {
            return $this->jsonError('Stripe is not configured. Please contact support.', 'STRIPE_NOT_CONFIGURED', null, 500);
        }

        try {
            Stripe::setApiKey($stripeSecret);

            // Prepare metadata for PaymentIntent
            $metadata = [
                'user_id' => (string)Auth::id(),
                'user_email' => Auth::user()->email ?? '',
            ];

            $paymentIntent = PaymentIntent::create([
                'amount' => (int)round($request->amount * 100), // Convert to cents
                'currency' => 'nzd', // Stripe accepts lowercase currency codes
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => $metadata,
            ]);

            if (!$paymentIntent->client_secret) {
                return $this->jsonError('Failed to create payment intent. Please try again.', 'PAYMENT_INTENT_CREATION_FAILED', null, 500);
            }

            return $this->jsonSuccess('Payment intent created successfully.', [
                'clientSecret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe PaymentIntent creation failed', [
                'error' => $e->getMessage(),
                'amount' => $request->amount,
                'stripe_error_code' => $e->getStripeCode(),
            ]);

            return $this->jsonError('Payment initialization failed. Please try again.', 'STRIPE_ERROR', null, 500);
        } catch (\Exception $e) {
            Log::error('PaymentIntent creation exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->jsonError('An error occurred while initializing payment. Please try again.', 'PAYMENT_INIT_ERROR', null, 500);
        }
    }

    // Process order and payment
    public function processOrder(StoreCheckoutRequest $request)
    {
        if (app()->environment('local')) {
            Log::info('Order processing started', [
                'user_id' => Auth::id(),
                'payment_intent_id' => $request->payment_intent_id ?? null,
                'cart_items_count' => CartItem::where('user_id', Auth::id())->count(),
            ]);
        }

        // Get cart items (authentication required)
        $cartItems = CartItem::with(['product'])
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            Log::warning('Order processing attempted with empty cart', [
                'user_id' => Auth::id(),
            ]);
            return $this->jsonError('Your cart is empty. Please add items to your cart before checkout.', 'CART_EMPTY', null, 400);
        }

        // Verify payment with Stripe - Read from database settings (with .env fallback)
        $stripeSecret = SettingHelper::get('stripe_secret', config('services.stripe.secret'));

        if (!$stripeSecret) {
            Log::error('Stripe secret key not configured');
            return $this->jsonError('Stripe is not configured. Please contact support.', 'STRIPE_NOT_CONFIGURED', null, 500);
        }

        // Validate payment_intent_id is present
        if (empty($request->payment_intent_id)) {
            Log::error('Payment intent ID is missing', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            return $this->jsonError('Payment intent ID is missing. Please try again.', 'PAYMENT_INTENT_MISSING', null, 400);
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

                return $this->jsonError('Payment not completed. Please try again.', 'PAYMENT_NOT_COMPLETED', ['status' => $paymentIntent->status], 400);
            }

            // Calculate totals using PriceCalculationService
            $subtotal = $this->priceService->calculateSubtotal($cartItems);

            $appliedCoupon = Session::get('applied_coupon');
            $discount = round((is_array($appliedCoupon) && isset($appliedCoupon['discount'])) ? (float)$appliedCoupon['discount'] : 0, 2);
            $couponCode = $appliedCoupon['code'] ?? null;
            $tax = 0.00;

            // Calculate shipping based on shipping region
            $orderAmount = round($subtotal - $discount, 2);
            $shippingInfo = $this->shippingService->calculateShippingWithInfo($request->shipping_region_id, $orderAmount);
            $shipping = round($shippingInfo['shipping_price'], 2);
            $shippingPrice = round($shippingInfo['shipping_price'], 2);

            // Calculate total using PriceCalculationService
            $total = $this->priceService->calculateTotal($subtotal, $discount, $shipping);

            // Verify total matches payment amount
            $paymentAmount = $paymentIntent->amount / 100; // Convert from cents
            $difference = abs($total - $paymentAmount);

            // Log the mismatch for debugging (only in local environment)
            if ($difference > 0.01 && app()->environment('local')) {
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
                $data = null;
                if (app()->environment('local')) {
                    $data = [
                        'calculated_total' => round($total, 2),
                        'payment_amount' => round($paymentAmount, 2),
                        'difference' => round($difference, 2)
                    ];
                }
                return $this->jsonError('Payment amount mismatch. Please refresh and try again.', 'PAYMENT_AMOUNT_MISMATCH', null, 400);
            }

            // Validate stock and prices before creating order
            $stockValidationErrors = [];
            $priceValidationErrors = [];
            
            if (app()->environment('local')) {
                Log::info('Validating cart items before order creation', [
                    'user_id' => Auth::id(),
                    'items_count' => $cartItems->count(),
                ]);
            }
            
            // Pre-load all products with lock to avoid N+1 queries during validation
            $productIds = $cartItems->pluck('product_id')->unique()->toArray();
            $products = Product::lockForUpdate()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');
            
            foreach ($cartItems as $cartItem) {
                // Get product from pre-loaded collection
                $product = $products->get($cartItem->product_id);
                
                if (!$product) {
                    $errorMsg = "Product no longer exists (ID: {$cartItem->product_id}).";
                    $stockValidationErrors[] = $errorMsg;
                    Log::warning('Product not found during order validation', [
                        'user_id' => Auth::id(),
                        'product_id' => $cartItem->product_id,
                        'cart_item_id' => $cartItem->id,
                    ]);
                    continue;
                }
                
                if (!$product->status) {
                    $errorMsg = "Product '{$product->name}' is no longer available.";
                    $stockValidationErrors[] = $errorMsg;
                    Log::warning('Inactive product in cart during order validation', [
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                    ]);
                    continue;
                }
                
                // Check stock availability
                if ($product->stock < $cartItem->quantity) {
                    $availableStock = max(0, $product->stock);
                    $errorMsg = "Product '{$product->name}': Only {$availableStock} item(s) available (requested: {$cartItem->quantity}).";
                    $stockValidationErrors[] = $errorMsg;
                    Log::warning('Insufficient stock during order validation', [
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'requested_quantity' => $cartItem->quantity,
                        'available_stock' => $availableStock,
                    ]);
                    continue;
                }
                
                // Validate price hasn't changed significantly
                $currentPrice = $product->discount_price ?? $product->total_price;
                $priceDifference = abs($currentPrice - $cartItem->price);
                
                // Allow small price differences (up to 0.05) due to rounding
                if ($priceDifference > 0.05) {
                    $errorMsg = "Product '{$product->name}' price has changed. Please refresh your cart.";
                    $priceValidationErrors[] = $errorMsg;
                    Log::warning('Price mismatch during order validation', [
                        'user_id' => Auth::id(),
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'cart_price' => $cartItem->price,
                        'current_price' => $currentPrice,
                        'difference' => $priceDifference,
                    ]);
                }
            }
            
            // Return errors if any validation failed
            if (!empty($stockValidationErrors) || !empty($priceValidationErrors)) {
                $allErrors = array_merge($stockValidationErrors, $priceValidationErrors);
                Log::warning('Order validation failed', [
                    'user_id' => Auth::id(),
                    'stock_errors_count' => count($stockValidationErrors),
                    'price_errors_count' => count($priceValidationErrors),
                ]);
                return $this->jsonError('Your cart contains items that are no longer available or have changed.', 'CART_VALIDATION_FAILED', ['errors' => $allErrors], 400);
            }

            // Create order
            DB::beginTransaction();

            try {
                // Re-lock products for stock decrement (products already loaded above, but need fresh lock)
                // Use the same product IDs from validation
                $productsForDecrement = Product::lockForUpdate()
                    ->whereIn('id', $productIds)
                    ->get()
                    ->keyBy('id');
                
                // Decrement stock for all products atomically
                foreach ($cartItems as $cartItem) {
                    $product = $productsForDecrement->get($cartItem->product_id);
                    
                    if (!$product) {
                        throw new \Exception("Product not found: {$cartItem->product_id}");
                    }
                    
                    // Double-check stock (in case it changed between validation and decrement)
                    if ($product->stock < $cartItem->quantity) {
                        Log::error('Stock insufficient during decrement', [
                            'user_id' => Auth::id(),
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'requested_quantity' => $cartItem->quantity,
                            'available_stock' => $product->stock,
                        ]);
                        throw new \Exception("Insufficient stock for product '{$product->name}'. Please refresh and try again.");
                    }
                    
                    // Decrement stock atomically
                    $oldStock = $product->stock;
                    $product->decrement('stock', $cartItem->quantity);
                    
                    if (app()->environment('local')) {
                        Log::info('Stock decremented for order', [
                            'user_id' => Auth::id(),
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity' => $cartItem->quantity,
                            'old_stock' => $oldStock,
                            'new_stock' => $product->fresh()->stock,
                        ]);
                    }
                }

                // Get order number before creating order (for metadata update)
                $orderNumber = Order::generateOrderNumber();
                
                // Extract payment method details from PaymentIntent
                $paymentMethodType = null;
                $paymentMethodId = null;
                $customerId = null;
                
                if (isset($paymentIntent->payment_method)) {
                    $paymentMethodId = $paymentIntent->payment_method;
                } elseif (isset($paymentIntent->latest_payment_intent_attempt->payment_method)) {
                    $paymentMethodId = $paymentIntent->latest_payment_intent_attempt->payment_method;
                }
                
                if (isset($paymentIntent->customer)) {
                    $customerId = $paymentIntent->customer;
                }
                
                // Payment method type will be retrieved from webhook for more reliable data
                // Webhook has access to complete charge details after payment is confirmed

                $order = Order::create([
                    'order_number' => $orderNumber,
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
                    'currency' => 'NZD',
                    'payment_method' => 'stripe',
                    'payment_status' => 'paid', // Will be confirmed by webhook
                    'payment_confirmed_at' => now(), // Set when order is created (webhook will confirm)
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                    'stripe_customer_id' => $customerId,
                    'stripe_payment_method_id' => $paymentMethodId,
                    'stripe_payment_method_type' => $paymentMethodType,
                    'status' => 'processing', // Start as processing when payment succeeded
                    'notes' => $request->notes ?? null,
                ]);
                
                // Update PaymentIntent metadata with order number (for better Stripe dashboard tracking)
                try {
                    PaymentIntent::update($paymentIntent->id, [
                        'metadata' => [
                            'user_id' => (string)Auth::id(),
                            'user_email' => Auth::user()->email ?? '',
                            'order_number' => $orderNumber,
                            'order_id' => (string)$order->id,
                        ],
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to update PaymentIntent metadata', [
                        'payment_intent_id' => $paymentIntent->id,
                        'error' => $e->getMessage(),
                    ]);
                }

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

                if (app()->environment('local')) {
                    Log::info('Order created successfully', [
                        'user_id' => Auth::id(),
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'total' => $order->total,
                        'payment_intent_id' => $paymentIntent->id,
                    ]);
                }

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

                // Create admin notification
                try {
                    $this->notificationService->createOrderNotification($order);
                } catch (\Exception $e) {
                    Log::error('Failed to create order notification: ' . $e->getMessage());
                }

                return $this->jsonSuccess('Order placed successfully!', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'redirect_url' => route('checkout.success', ['order' => $order->order_number])
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                
                Log::error('Order creation failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => Auth::id(),
                    'payment_intent_id' => $request->payment_intent_id ?? null,
                ]);
                
                // Provide user-friendly error message
                $errorMessage = 'Failed to process your order. ';
                if (str_contains($e->getMessage(), 'stock') || str_contains($e->getMessage(), 'available')) {
                    $errorMessage = $e->getMessage();
                } elseif (str_contains($e->getMessage(), 'price')) {
                    $errorMessage = 'Product prices have changed. Please refresh your cart and try again.';
                } else {
                    $errorMessage .= 'Please try again or contact support if the problem persists.';
                }
                
                return $this->jsonError($errorMessage, 'ORDER_PROCESSING_ERROR', null, 400);
            }

        } catch (ApiErrorException $e) {
            Log::error('Stripe PaymentIntent verification failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $request->payment_intent_id ?? 'missing',
                'stripe_error_code' => $e->getStripeCode(),
            ]);

            return $this->jsonError('Payment verification failed. Please try again.', 'PAYMENT_VERIFICATION_ERROR', null, 500);
        } catch (\Exception $e) {
            Log::error('Payment verification exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->jsonError('An unexpected error occurred. Please try again.', 'PAYMENT_VERIFICATION_EXCEPTION', null, 500);
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
                    $stripeSecret = SettingHelper::get('stripe_secret', config('services.stripe.secret'));
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

    /**
     * Search addresses using NZ Post API
     */
    public function searchAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:3|max:255',
        ]);

        $query = (string) $validated['query'];

        try {
            $results = $this->nzPostService->searchAddresses($query);
            
            return $this->jsonSuccess('Address search completed.', ['results' => $results]);
        } catch (\Exception $e) {
            Log::error('Address search error', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);

            return $this->jsonError('Address search failed. Please try again.', 'ADDRESS_SEARCH_ERROR', ['results' => []], 500);
        }
    }

    /**
     * Get address details by ID
     */
    public function getAddress(string $id): JsonResponse
    {
        try {
            $address = $this->nzPostService->getAddressDetails($id);
            
            if ($address) {
                return $this->jsonSuccess('Address details retrieved.', ['address' => $address]);
            }

            return $this->jsonError('Address not found', 'ADDRESS_NOT_FOUND', null, 404);
        } catch (\Exception $e) {
            Log::error('Get address error', [
                'error' => $e->getMessage(),
                'address_id' => $id
            ]);

            return $this->jsonError('Failed to get address details', 'ADDRESS_GET_ERROR', null, 500);
        }
    }
}
