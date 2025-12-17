<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    /**
     * Display the account page (redirects to view profile)
     */
    public function index(): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        return redirect()->route('account.view-profile');
    }

    /**
     * Display view profile page
     */
    public function viewProfile(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        $user->load('userDetail');

        return view('frontend.account.view-profile', compact('user'));
    }

    /**
     * Display edit profile page
     */
    public function editProfile(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        $user->load('userDetail');

        return view('frontend.account.edit-profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other,prefer-not-to-say',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('avatars', $imageName, 'public');

            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $updateData['avatar'] = $imagePath;
        }

        $user->update($updateData);

        // Prepare user detail data - ensure empty strings are converted to null
        $userDetailData = [
            'phone' => !empty($validated['phone']) ? trim($validated['phone']) : null,
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

    /**
     * Display change password page
     */
    public function changePassword(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();

        return view('frontend.account.change-password', compact('user'));
    }

    /**
     * Update user password
     */
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

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('account.view-profile')
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Display manage addresses page
     */
    public function manageAddresses(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

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

        return view('frontend.account.manage-addresses', compact('user', 'regions', 'billingAddresses', 'shippingAddresses'));
    }

    /**
     * Store new address
     */
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
            'email' => 'nullable|email|max:255',
            'street_address' => 'required|string|max:255',
            'suburb' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'zip_code' => 'required|string|max:20',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // If this is set as default, unset other defaults of the same type
        if ($request->has('is_default') && $request->is_default) {
            UserAddress::where('user_id', $user->id)
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        UserAddress::create([
            'user_id' => $user->id,
            'type' => $validated['type'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'street_address' => $validated['street_address'],
            'suburb' => $validated['suburb'] ?? null,
            'city' => $validated['city'],
            'region_id' => $validated['region_id'],
            'zip_code' => $validated['zip_code'],
            'country' => 'New Zealand',
            'is_default' => $request->has('is_default') && $request->is_default ? true : false,
        ]);

        return redirect()->route('account.manage-addresses')
            ->with('success', 'Address added successfully!');
    }

    /**
     * Edit address - return JSON for AJAX
     */
    public function editAddress(int $id)
    {
        if (!Auth::check()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        $address = UserAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->with('region')
            ->firstOrFail();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
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

    /**
     * Update address
     */
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
            'email' => 'nullable|email|max:255',
            'street_address' => 'required|string|max:255',
            'suburb' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'zip_code' => 'required|string|max:20',
            'is_default' => 'nullable|boolean',
        ]);

        // If this is set as default, unset other defaults of the same type
        if ($request->has('is_default') && $request->is_default) {
            UserAddress::where('user_id', $user->id)
                ->where('type', $validated['type'])
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $address->update([
            'type' => $validated['type'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'street_address' => $validated['street_address'],
            'suburb' => $validated['suburb'] ?? null,
            'city' => $validated['city'],
            'region_id' => $validated['region_id'],
            'zip_code' => $validated['zip_code'],
            'country' => 'New Zealand',
            'is_default' => $request->has('is_default') && $request->is_default ? true : false,
        ]);

        return redirect()->route('account.manage-addresses')
            ->with('success', 'Address updated successfully!');
    }

    /**
     * Delete address
     */
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

    /**
     * Set address as default
     */
    public function setDefaultAddress(int $id): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        $address = UserAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Unset other defaults of the same type
        UserAddress::where('user_id', $user->id)
            ->where('type', $address->type)
            ->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return redirect()->route('account.manage-addresses')
            ->with('success', 'Default address updated successfully!');
    }

    /**
     * Display my orders page
     */
    public function myOrders(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        
        // Get orders for the logged-in user, ordered by most recent first
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Load product images efficiently
        $productIds = $orders->pluck('items')->flatten()->pluck('product_id')->unique()->filter();
        if ($productIds->isNotEmpty()) {
            $productImages = DB::table('products_images')
                ->select('product_id', DB::raw('MIN(id) as min_id'))
                ->whereIn('product_id', $productIds)
                ->groupBy('product_id')
                ->pluck('min_id', 'product_id');
            
            $images = DB::table('products_images')
                ->whereIn('id', $productImages->values())
                ->select('id', 'product_id', 'image')
                ->get()
                ->keyBy('product_id');
            
            // Attach first image to each product
            $orders->each(function($order) use ($images) {
                $order->items->each(function($item) use ($images) {
                    if ($item->product) {
                        if ($images->has($item->product_id)) {
                            $item->product->setAttribute('main_image', asset('storage/' . $images[$item->product_id]->image));
                        } else {
                            $item->product->setAttribute('main_image', asset('assets/images/placeholder.jpg'));
                        }
                    }
                });
            });
        }

        return view('frontend.account.my-orders', compact('user', 'orders'));
    }

    /**
     * Display order details page
     */
    public function orderDetails(string $orderNumber): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your account.');
        }

        $user = Auth::user();
        
        // Get order by order number and ensure it belongs to the user
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with(['items.product', 'billingRegion', 'shippingRegion'])
            ->firstOrFail();
        
        // Load product images efficiently
        $productIds = $order->items->pluck('product_id')->unique()->filter();
        if ($productIds->isNotEmpty()) {
            $productImages = DB::table('products_images')
                ->select('product_id', DB::raw('MIN(id) as min_id'))
                ->whereIn('product_id', $productIds)
                ->groupBy('product_id')
                ->pluck('min_id', 'product_id');
            
            $images = DB::table('products_images')
                ->whereIn('id', $productImages->values())
                ->select('id', 'product_id', 'image')
                ->get()
                ->keyBy('product_id');
            
            // Attach first image to each product
            $order->items->each(function($item) use ($images) {
                if ($item->product) {
                    if ($images->has($item->product_id)) {
                        $item->product->setAttribute('main_image', asset('storage/' . $images[$item->product_id]->image));
                    } else {
                        $item->product->setAttribute('main_image', asset('assets/images/placeholder.jpg'));
                    }
                }
            });
        }

        return view('frontend.account.order-details', compact('user', 'order'));
    }
}
