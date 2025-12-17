<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get the identifier for the current cart (user_id or session_id)
     */
    private function getCartIdentifier(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id(), 'session_id' => null];
        }

        // Generate or get session ID for guest
        $sessionId = Session::getId();
        return ['user_id' => null, 'session_id' => $sessionId];
    }

    /**
     * Add product to cart
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:99'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is active
        if (!$product->status) {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available.'
            ], 400);
        }

        $quantity = $request->quantity ?? 1;
        $cartIdentifier = $this->getCartIdentifier();

        // Get effective price (discount_price if available, otherwise total_price)
        $price = $product->discount_price ?? $product->total_price;

        // Check if product already exists in cart
        $existingCartItem = CartItem::where('product_id', $product->id)
            ->when($cartIdentifier['user_id'], function($query) use ($cartIdentifier) {
                return $query->where('user_id', $cartIdentifier['user_id']);
            })
            ->when($cartIdentifier['session_id'], function($query) use ($cartIdentifier) {
                return $query->where('session_id', $cartIdentifier['session_id']);
            })
            ->first();

        if ($existingCartItem) {
            // Update quantity if item already exists
            $existingCartItem->quantity += $quantity;
            $existingCartItem->save();
        } else {
            // Create new cart item
            CartItem::create([
                'user_id' => $cartIdentifier['user_id'],
                'session_id' => $cartIdentifier['session_id'],
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price
            ]);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $cartIdentifier = $this->getCartIdentifier();

        $cartItem = CartItem::where('id', $request->cart_item_id)
            ->when($cartIdentifier['user_id'], function($query) use ($cartIdentifier) {
                return $query->where('user_id', $cartIdentifier['user_id']);
            })
            ->when($cartIdentifier['session_id'], function($query) use ($cartIdentifier) {
                return $query->where('session_id', $cartIdentifier['session_id']);
            })
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully.',
            'cart_count' => $cartCount,
            'subtotal' => $cartItem->subtotal
        ]);
    }

    /**
     * Remove product from cart
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id'
        ]);

        $cartIdentifier = $this->getCartIdentifier();

        $cartItem = CartItem::where('id', $request->cart_item_id)
            ->when($cartIdentifier['user_id'], function($query) use ($cartIdentifier) {
                return $query->where('user_id', $cartIdentifier['user_id']);
            })
            ->when($cartIdentifier['session_id'], function($query) use ($cartIdentifier) {
                return $query->where('session_id', $cartIdentifier['session_id']);
            })
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.'
            ], 404);
        }

        $cartItem->delete();

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully.',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Display cart page
     */
    public function index()
    {
        $title = 'Shopping Cart';
        $cartIdentifier = $this->getCartIdentifier();

        // Optimized query - load products with minimal data and first image only
        $cartItems = CartItem::with(['product' => function($query) {
                $query->select('id', 'name', 'slug', 'total_price', 'discount_price', 'barcode');
            }])
            ->when($cartIdentifier['user_id'], function($query) use ($cartIdentifier) {
                return $query->where('user_id', $cartIdentifier['user_id']);
            })
            ->when($cartIdentifier['session_id'], function($query) use ($cartIdentifier) {
                return $query->where('session_id', $cartIdentifier['session_id']);
            })
            ->get();

        // Eager load first image for each product efficiently
        $productIds = $cartItems->pluck('product_id')->unique();
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

            // Attach first image URL to each product
            $cartItems->each(function($item) use ($images) {
                if ($item->product) {
                    if ($images->has($item->product_id)) {
                        $item->product->setAttribute('main_image', asset('storage/' . $images[$item->product_id]->image));
                    } else {
                        $item->product->setAttribute('main_image', asset('assets/images/placeholder.jpg'));
                    }
                }
            });
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->subtotal;
        });

        // $shipping = 5.00; // Fixed shipping for now
        $shipping = 0.00; // Shipping commented out for now
        $total = $subtotal + $shipping;

        return view('frontend.cart.cart', compact('title', 'cartItems', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Get cart items (AJAX)
     */
    public function list(): JsonResponse
    {
        $cartIdentifier = $this->getCartIdentifier();

        $query = CartItem::join('products', 'cart_items.product_id', '=', 'products.id')
            ->select(
                'cart_items.id',
                'cart_items.product_id',
                'cart_items.quantity',
                'cart_items.price',
                'products.name as product_name',
                'products.slug as product_slug',
                DB::raw('(SELECT image FROM products_images WHERE product_id = products.id ORDER BY id ASC LIMIT 1) as product_image_path')
            )
            ->when($cartIdentifier['user_id'], function($query) use ($cartIdentifier) {
                return $query->where('cart_items.user_id', $cartIdentifier['user_id']);
            })
            ->when($cartIdentifier['session_id'], function($query) use ($cartIdentifier) {
                return $query->where('cart_items.session_id', $cartIdentifier['session_id']);
            });

        $cartItems = $query->get()
            ->map(function ($item) {
                // Format image URL similar to ProductImage accessor
                $productImage = $item->product_image_path
                    ? asset('storage/' . $item->product_image_path)
                    : asset('assets/images/placeholder.jpg');

                // Calculate subtotal
                $subtotal = $item->price * $item->quantity;

                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_slug' => $item->product_slug,
                    'product_image' => $productImage,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $subtotal,
                    'product_url' => route('product.detail', $item->product_slug),
                ];
            });

        $total = $cartItems->sum('subtotal');

        return response()->json([
            'success' => true,
            'items' => $cartItems,
            'count' => $cartItems->count(),
            'total' => $total
        ]);
    }

    /**
     * Get cart count
     */
    public function count(): JsonResponse
    {
        $count = $this->getCartCount();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get cart count helper
     */
    private function getCartCount(): int
    {
        $cartIdentifier = $this->getCartIdentifier();

        return CartItem::when($cartIdentifier['user_id'], function($query) use ($cartIdentifier) {
                return $query->where('user_id', $cartIdentifier['user_id']);
            })
            ->when($cartIdentifier['session_id'], function($query) use ($cartIdentifier) {
                return $query->where('session_id', $cartIdentifier['session_id']);
            })
            ->sum('quantity');
    }
}
