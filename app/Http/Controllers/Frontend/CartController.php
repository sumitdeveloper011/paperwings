<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\CartService;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\CartItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    // Add product to cart
    public function add(AddToCartRequest $request): JsonResponse
    {
        try {
            $cartIdentifier = $this->cartService->getCartIdentifier();
            $this->cartService->addToCart(
                $request->product_id,
                $request->quantity ?? 1,
                $cartIdentifier
            );

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully.',
                'cart_count' => $this->cartService->getCartCount($cartIdentifier)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Update cart item quantity
    public function update(UpdateCartRequest $request): JsonResponse
    {
        try {
            $cartIdentifier = $this->cartService->getCartIdentifier();
            $cartItem = $this->cartService->updateCartItem(
                $request->cart_item_id,
                $request->quantity,
                $cartIdentifier
            );

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully.',
                'cart_count' => $this->cartService->getCartCount($cartIdentifier),
                'subtotal' => $cartItem->subtotal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    // Remove product from cart
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id'
        ]);

        try {
            $cartIdentifier = $this->cartService->getCartIdentifier();
            $this->cartService->removeFromCart($request->cart_item_id, $cartIdentifier);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart successfully.',
                'cart_count' => $this->cartService->getCartCount($cartIdentifier)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    // Display cart page
    public function index()
    {
        $title = 'Shopping Cart';
        $cartIdentifier = $this->cartService->getCartIdentifier();
        $cartItems = $this->cartService->getCartItems($cartIdentifier);

        // Use ProductImageService for efficient image loading
        $productIds = $cartItems->pluck('product_id')->unique();
        if ($productIds->isNotEmpty()) {
            $images = \App\Services\ProductImageService::getFirstImagesForProducts($productIds);

            // Attach first image URL to each product
            $cartItems->each(function($item) use ($images) {
                if ($item->product) {
                    $image = $images->get($item->product_id);
                    $item->product->setAttribute('main_image',
                        $image ? $image->image_url : asset('assets/images/placeholder.jpg')
                    );
                }
            });
        }

        $subtotal = $this->cartService->getCartSubtotal($cartIdentifier);
        $shipping = 0.00; // Shipping commented out for now
        $total = $subtotal + $shipping;

        return view('frontend.cart.cart', compact('title', 'cartItems', 'subtotal', 'shipping', 'total'));
    }

    // Get cart items (AJAX)
    public function list(): JsonResponse
    {
        $cartIdentifier = $this->cartService->getCartIdentifier();
        $cartItems = $this->cartService->getCartItems($cartIdentifier);

        // Use API Resource for consistent response format
        $cartItemsArray = CartItemResource::collection($cartItems)->resolve();
        $total = collect($cartItemsArray)->sum('subtotal');

        return response()->json([
            'success' => true,
            'items' => $cartItemsArray,
            'count' => count($cartItemsArray),
            'total' => $total
        ]);
    }

    // Get cart count
    public function count(): JsonResponse
    {
        $cartIdentifier = $this->cartService->getCartIdentifier();
        $count = $this->cartService->getCartCount($cartIdentifier);

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    // Render cart items as HTML (for AJAX)
    public function render(): JsonResponse
    {
        $cartIdentifier = $this->cartService->getCartIdentifier();
        $cartItems = $this->cartService->getCartItems($cartIdentifier)
            ->filter(function($item) {
                return $item->product !== null;
            });

        // Render HTML using Blade partial
        $html = view('frontend.cart.partials.items', [
            'items' => $cartItems
        ])->render();

        $total = $cartItems->sum(fn($item) => $item->subtotal);

        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $this->cartService->getCartCount($cartIdentifier),
            'total' => $total
        ]);
    }

    // Check if product(s) is in cart
    public function check(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'status' => []
            ]);
        }

        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        $cartIdentifier = $this->cartService->getCartIdentifier();
        $status = $this->cartService->checkProductsInCart(
            $cartIdentifier,
            $request->product_ids
        );

        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }
}
