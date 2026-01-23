<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserDetail;
use App\Services\NZPostAddressService;
use App\Services\CartService;
use App\Mail\OrderCancelledMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\ImageService;
use App\Helpers\CommonHelper;

class AccountController extends Controller
{
    protected ImageService $imageService;
    protected NZPostAddressService $nzPostService;
    protected CartService $cartService;

    public function __construct(
        ImageService $imageService, 
        NZPostAddressService $nzPostService,
        CartService $cartService
    ) {
        $this->imageService = $imageService;
        $this->nzPostService = $nzPostService;
        $this->cartService = $cartService;
    }

    // Display the account page (redirects to view profile)
    public function index(): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        return redirect()->route('account.view-profile');
    }

    // Display view profile page
    public function viewProfile(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $title = 'My Profile';
        $user = Auth::user();
        $user->load('userDetail');

        return view('frontend.account.view-profile', compact('title', 'user'));
    }

    // Display edit profile page
    public function editProfile(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $title = 'Edit Profile';
        $user = Auth::user();
        $user->load('userDetail');

        return view('frontend.account.edit-profile', compact('title', 'user'));
    }

    // Update user profile
    public function updateProfile(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email:dns|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other,prefer-not-to-say',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Sanitize all text inputs
        $firstName = CommonHelper::sanitizeName($validated['first_name']);
        $lastName = CommonHelper::sanitizeName($validated['last_name']);
        $email = CommonHelper::sanitizeEmail($validated['email']);
        $phone = !empty($validated['phone']) ? CommonHelper::sanitizePhone($validated['phone']) : null;

        // Security checks - SQL Injection and XSS detection for all inputs
        $securityViolation = false;
        $violatedInput = '';

        if (CommonHelper::detectSqlInjection($firstName) || CommonHelper::detectXss($firstName)) {
            $securityViolation = true;
            $violatedInput = 'first_name';
        }

        if (CommonHelper::detectSqlInjection($lastName) || CommonHelper::detectXss($lastName)) {
            $securityViolation = true;
            $violatedInput = 'last_name';
        }

        if (CommonHelper::detectSqlInjection($email) || CommonHelper::detectXss($email)) {
            $securityViolation = true;
            $violatedInput = 'email';
        }

        if ($phone && (CommonHelper::detectSqlInjection($phone) || CommonHelper::detectXss($phone))) {
            $securityViolation = true;
            $violatedInput = 'phone';
        }

        if ($securityViolation) {
            CommonHelper::logSecurityEvent('Malicious input detected in profile update attempt', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'input_type' => $violatedInput
            ]);

            return back()->withErrors([
                $violatedInput => 'Invalid input detected. Please check your information.'
            ])->withInput();
        }

        $updateData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ];

        if ($request->hasFile('avatar')) {
            $imagePath = $this->imageService->uploadSimple(
                $request->file('avatar'),
                'avatars',
                $user->avatar
            );
            if ($imagePath) {
                $updateData['avatar'] = $imagePath;
            }
        }

        $user->update($updateData);

        $userDetailData = [
            'phone' => $phone,
            'date_of_birth' => !empty($validated['date_of_birth']) && $validated['date_of_birth'] !== '' ? $validated['date_of_birth'] : null,
            'gender' => !empty($validated['gender']) ? $validated['gender'] : null,
        ];

        $userDetail = UserDetail::updateOrCreate(
            ['user_id' => $user->id],
            $userDetailData
        );

        return redirect()->route('account.view-profile')
            ->with('success', 'Profile updated successfully!');
    }

    // Display change password page
    public function changePassword(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $title = 'Change Password';
        $user = Auth::user();

        return view('frontend.account.change-password', compact('title', 'user'));
    }

    // Update user password
    public function updatePassword(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.mixed' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.uncompromised' => 'The password is too common. Please choose a different password.',
        ]);

        // Security checks - SQL Injection detection for passwords
        $currentPassword = $request->current_password;
        $newPassword = $request->password;

        if (CommonHelper::detectSqlInjection($currentPassword) || CommonHelper::detectSqlInjection($newPassword)) {
            CommonHelper::logSecurityEvent('Malicious input detected in password change attempt', Auth::user(), [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'input_type' => 'password'
            ]);

            return back()->withErrors(['password' => 'Invalid input detected.'])->withInput();
        }

        $user = Auth::user();

        if (!Hash::check($currentPassword, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        CommonHelper::logSecurityEvent('Password changed successfully', $user, [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('account.view-profile')
            ->with('success', 'Password updated successfully!');
    }

    // Display manage addresses page
    public function manageAddresses(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $title = 'Manage Addresses';
        $user = Auth::user();
        $regions = Region::active()->orderBy('name', 'asc')->get();
        $billingAddresses = UserAddress::where('user_id', $user->id)
            ->where('type', 'billing')
            ->with('region')
            ->get();
        $shippingAddresses = UserAddress::where('user_id', $user->id)
            ->where('type', 'shipping')
            ->with('region')
            ->get();

        return view('frontend.account.manage-addresses', compact('title', 'user', 'regions', 'billingAddresses', 'shippingAddresses'));
    }

    // Store new address
    public function storeAddress(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $validated = $request->validate([
            'type' => 'required|in:billing,shipping',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email:dns|max:255',
            'street_address' => 'required|string|max:500',
            'suburb' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'zip_code' => 'required|string|max:20',
            'is_default' => 'nullable|boolean',
        ]);

        // Sanitize all text inputs
        $firstName = CommonHelper::sanitizeName($validated['first_name']);
        $lastName = CommonHelper::sanitizeName($validated['last_name']);
        $phone = CommonHelper::sanitizePhone($validated['phone']);
        $email = !empty($validated['email']) ? CommonHelper::sanitizeEmail($validated['email']) : null;
        $streetAddress = CommonHelper::sanitizeInput($validated['street_address']);
        $suburb = !empty($validated['suburb']) ? CommonHelper::sanitizeInput($validated['suburb']) : null;
        $city = CommonHelper::sanitizeInput($validated['city']);
        $zipCode = CommonHelper::sanitizeInput($validated['zip_code']);

        // Security checks - SQL Injection and XSS detection for all inputs
        $securityViolation = false;
        $violatedInput = '';

        $inputsToCheck = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'street_address' => $streetAddress,
            'city' => $city,
            'zip_code' => $zipCode,
        ];

        if ($email) {
            $inputsToCheck['email'] = $email;
        }
        if ($suburb) {
            $inputsToCheck['suburb'] = $suburb;
        }

        foreach ($inputsToCheck as $field => $value) {
            if (CommonHelper::detectSqlInjection($value) || CommonHelper::detectXss($value)) {
                $securityViolation = true;
                $violatedInput = $field;
                break;
            }
        }

        if ($securityViolation) {
            CommonHelper::logSecurityEvent('Malicious input detected in address creation attempt', Auth::user(), [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'input_type' => $violatedInput
            ]);

            return back()->withErrors([
                $violatedInput => 'Invalid input detected. Please check your information.'
            ])->withInput();
        }

        $user = Auth::user();

        if ($request->has('is_default') && $request->is_default) {
            UserAddress::where('user_id', $user->id)
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        UserAddress::create([
            'user_id' => $user->id,
            'type' => $validated['type'],
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'email' => $email,
            'street_address' => $streetAddress,
            'suburb' => $suburb,
            'city' => $city,
            'region_id' => $validated['region_id'],
            'zip_code' => $zipCode,
            'country' => 'New Zealand',
            'is_default' => $request->has('is_default') && $request->is_default ? true : false,
        ]);

        return redirect()->route('account.manage-addresses')
            ->with('success', 'Address added successfully!');
    }

    // Edit address - return JSON for AJAX
    public function editAddress(int $id)
    {
        if (!Auth::check()) {
            if (request()->expectsJson()) {
                return $this->jsonError('Unauthorized', 'UNAUTHENTICATED', null, 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        $address = UserAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->with('region')
            ->firstOrFail();

        if (request()->expectsJson() || request()->ajax()) {
            return $this->jsonSuccess('Address retrieved.', [
                'address' => [
                    'id' => $address->id,
                    'type' => $address->type,
                    'first_name' => $address->first_name,
                    'last_name' => $address->last_name,
                    'phone' => $address->phone,
                    'email' => $address->email,
                    'street_address' => $address->street_address,
                    'suburb' => $address->suburb,
                    'city' => $address->city,
                    'region_id' => $address->region_id,
                    'zip_code' => $address->zip_code,
                    'is_default' => $address->is_default,
                ]
            ]);
        }

        $regions = Region::active()->orderBy('name', 'asc')->get();
        $billingAddresses = UserAddress::where('user_id', $user->id)
            ->where('type', 'billing')
            ->with('region')
            ->get();
        $shippingAddresses = UserAddress::where('user_id', $user->id)
            ->where('type', 'shipping')
            ->with('region')
            ->get();

        return view('frontend.account.manage-addresses', compact('user', 'regions', 'billingAddresses', 'shippingAddresses', 'address'));
    }

    // Update address
    public function updateAddress(Request $request, int $id): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        $address = UserAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $validated = $request->validate([
            'type' => 'required|in:billing,shipping',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email:dns|max:255',
            'street_address' => 'required|string|max:500',
            'suburb' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'zip_code' => 'required|string|max:20',
            'is_default' => 'nullable|boolean',
        ]);

        // Sanitize all text inputs
        $firstName = CommonHelper::sanitizeName($validated['first_name']);
        $lastName = CommonHelper::sanitizeName($validated['last_name']);
        $phone = CommonHelper::sanitizePhone($validated['phone']);
        $email = !empty($validated['email']) ? CommonHelper::sanitizeEmail($validated['email']) : null;
        $streetAddress = CommonHelper::sanitizeInput($validated['street_address']);
        $suburb = !empty($validated['suburb']) ? CommonHelper::sanitizeInput($validated['suburb']) : null;
        $city = CommonHelper::sanitizeInput($validated['city']);
        $zipCode = CommonHelper::sanitizeInput($validated['zip_code']);

        // Security checks - SQL Injection and XSS detection for all inputs
        $securityViolation = false;
        $violatedInput = '';

        $inputsToCheck = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'street_address' => $streetAddress,
            'city' => $city,
            'zip_code' => $zipCode,
        ];

        if ($email) {
            $inputsToCheck['email'] = $email;
        }
        if ($suburb) {
            $inputsToCheck['suburb'] = $suburb;
        }

        foreach ($inputsToCheck as $field => $value) {
            if (CommonHelper::detectSqlInjection($value) || CommonHelper::detectXss($value)) {
                $securityViolation = true;
                $violatedInput = $field;
                break;
            }
        }

        if ($securityViolation) {
            CommonHelper::logSecurityEvent('Malicious input detected in address update attempt', $user, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'input_type' => $violatedInput
            ]);

            return back()->withErrors([
                $violatedInput => 'Invalid input detected. Please check your information.'
            ])->withInput();
        }

        if ($request->has('is_default') && $request->is_default) {
            UserAddress::where('user_id', $user->id)
                ->where('type', $validated['type'])
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update([
            'type' => $validated['type'],
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'email' => $email,
            'street_address' => $streetAddress,
            'suburb' => $suburb,
            'city' => $city,
            'region_id' => $validated['region_id'],
            'zip_code' => $zipCode,
            'country' => 'New Zealand',
            'is_default' => $request->has('is_default') && $request->is_default ? true : false,
        ]);

        return redirect()->route('account.manage-addresses')
            ->with('success', 'Address updated successfully!');
    }

    // Delete address
    public function destroyAddress(int $id): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        $address = UserAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $address->delete();

        return redirect()->route('account.manage-addresses')
            ->with('success', 'Address deleted successfully!');
    }

    // Set address as default
    public function setDefaultAddress(int $id): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        $address = UserAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        UserAddress::where('user_id', $user->id)
            ->where('type', $address->type)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        return redirect()->route('account.manage-addresses')
            ->with('success', 'Default address updated successfully!');
    }

    // Display my orders page
    public function myOrders(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $title = 'My Orders';
        $user = Auth::user();
        
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $productIds = $orders->pluck('items')->flatten()->pluck('product_id')->unique()->filter();
        if ($productIds->isNotEmpty()) {
            $images = \App\Services\ProductImageService::getFirstImagesForProducts($productIds);
            
            $orders->each(function($order) use ($images) {
                $order->items->each(function($item) use ($images) {
                    if ($item->product) {
                        $image = $images->get($item->product_id);
                        $item->product->setAttribute('main_image', 
                            $image ? $image->image_url : asset('assets/images/placeholder.jpg')
                        );
                    }
                });
            });
        }

        return view('frontend.account.my-orders', compact('title', 'user', 'orders'));
    }

    // Display order details page
    public function orderDetails(string $orderNumber): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with(['items.product', 'billingRegion', 'shippingRegion'])
            ->firstOrFail();
        
        $title = 'Order #' . $order->order_number;
        
        $productIds = $order->items->pluck('product_id')->unique()->filter();
        if ($productIds->isNotEmpty()) {
            $images = \App\Services\ProductImageService::getFirstImagesForProducts($productIds);
            
            $order->items->each(function($item) use ($images) {
                if ($item->product) {
                    $image = $images->get($item->product_id);
                    $item->product->setAttribute('main_image', 
                        $image ? $image->image_url : asset('assets/images/placeholder.jpg')
                    );
                }
            });
        }

        return view('frontend.account.order-details', compact('title', 'user', 'order'));
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
            
            return response()->json([
                'success' => true,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Address search error', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Address search failed. Please try again.',
                'results' => []
            ], 500);
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
                return response()->json([
                    'success' => true,
                    'address' => $address
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Get address error', [
                'error' => $e->getMessage(),
                'address_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get address details'
            ], 500);
        }
    }

    /**
     * Reorder - Add all items from a previous order to cart
     */
    public function reorder(string $orderNumber): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return $this->jsonError('Please login to access this feature.', 'UNAUTHORIZED', null, 401);
            }

            $user = Auth::user();
            
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', $user->id)
                ->with(['items.product'])
                ->first();

            if (!$order) {
                return $this->jsonError('Order not found.', 'ORDER_NOT_FOUND', null, 404);
            }

            if (!$order->items || $order->items->isEmpty()) {
                return $this->jsonError('This order has no items to reorder.', 'NO_ITEMS', null, 400);
            }

            $productIds = [];
            $quantities = [];
            $failedItems = [];

            foreach ($order->items as $item) {
                if (!$item->product_id) {
                    $failedItems[] = [
                        'product_name' => $item->product_name ?? 'Unknown Product',
                        'reason' => 'Product no longer exists'
                    ];
                    continue;
                }

                $product = $item->product;
                if (!$product || !$product->status) {
                    $failedItems[] = [
                        'product_name' => $item->product_name ?? 'Unknown Product',
                        'reason' => 'Product is no longer available'
                    ];
                    continue;
                }

                $productIds[] = $item->product_id;
                $quantities[] = $item->quantity;
            }

            if (empty($productIds)) {
                return $this->jsonError('No available products found in this order.', 'NO_AVAILABLE_PRODUCTS', [
                    'failed_items' => $failedItems
                ], 400);
            }

            $cartIdentifier = $this->cartService->getCartIdentifier();
            $results = $this->cartService->addMultipleToCart(
                $productIds,
                $quantities,
                $cartIdentifier
            );

            $successCount = count($results['success']);
            $totalFailedCount = count($results['failed']) + count($failedItems);

            if ($successCount === 0) {
                return $this->jsonError('Failed to add items to cart. Please try again.', 'CART_ADD_FAILED', [
                    'failed_items' => array_merge($failedItems, $results['failed'])
                ], 400);
            }

            $message = $successCount > 0 
                ? ($totalFailedCount > 0 
                    ? "{$successCount} item(s) added to cart successfully. {$totalFailedCount} item(s) could not be added."
                    : "All {$successCount} item(s) added to cart successfully!")
                : 'Failed to add items to cart.';

            return $this->jsonSuccess($message, [
                'cart_count' => $this->cartService->getCartCount($cartIdentifier),
                'results' => [
                    'success' => $results['success'],
                    'failed' => array_merge($failedItems, $results['failed'])
                ],
                'redirect_url' => route('cart.index')
            ]);

        } catch (\Exception $e) {
            Log::error('Reorder failed', [
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->jsonError('Failed to reorder. Please try again.', 'REORDER_ERROR', null, 500);
        }
    }

    /**
     * Cancel order - Allow users to cancel their own orders
     */
    public function cancelOrder(string $orderNumber): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return $this->jsonError('Please login to access this feature.', 'UNAUTHORIZED', null, 401);
            }

            $user = Auth::user();
            
            $order = Order::where('order_number', $orderNumber)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return $this->jsonError('Order not found.', 'ORDER_NOT_FOUND', null, 404);
            }

            // Only allow cancellation for pending or processing orders
            if (!in_array($order->status, ['pending', 'processing'])) {
                return $this->jsonError(
                    'This order cannot be cancelled. Only pending or processing orders can be cancelled.',
                    'CANCEL_NOT_ALLOWED',
                    ['current_status' => $order->status],
                    400
                );
            }

            // Update order status
            $oldStatus = $order->status;
            $order->update([
                'status' => 'cancelled'
            ]);

            Log::info('Order cancelled by user', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'user_id' => $user->id
            ]);

            // Send cancellation email
            try {
                Mail::to($order->billing_email)->queue(new OrderCancelledMail($order));
            } catch (\Exception $e) {
                Log::error('Failed to send order cancellation email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }

            $refundMessage = '';
            if ($order->payment_status === 'paid') {
                $refundMessage = ' A refund will be processed to your original payment method within 5-7 business days.';
            }

            return $this->jsonSuccess(
                'Order cancelled successfully.' . $refundMessage,
                [
                    'order_number' => $order->order_number,
                    'status' => $order->status
                ]
            );

        } catch (\Exception $e) {
            Log::error('Cancel order failed', [
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->jsonError('Failed to cancel order. Please try again or contact support.', 'CANCEL_ERROR', null, 500);
        }
    }
}
