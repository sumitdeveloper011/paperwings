<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\CartService;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\AddMultipleToCartRequest;
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
            $product = Product::where('uuid', $request->product_uuid)->firstOrFail();
            
            $cartIdentifier = $this->cartService->getCartIdentifier();
            $this->cartService->addToCart(
                $product->id,
                $request->quantity ?? 1,
                $cartIdentifier
            );

            $product->load(['category', 'brand']);

            return $this->jsonSuccess('Product added to cart successfully.', [
                'cart_count' => $this->cartService->getCartCount($cartIdentifier),
                'product' => $product ? [
                    'id' => $product->id,
                    'uuid' => $product->uuid,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'brand' => $product->brand->name ?? '',
                    'price' => $product->price,
                    'quantity' => $request->quantity ?? 1
                ] : null
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 'CART_ADD_ERROR', null, 400);
        }
    }

    // Add multiple products to cart (batch operation)
    public function addMultiple(AddMultipleToCartRequest $request): JsonResponse
    {
        try {
            // Get products with UUIDs
            $products = Product::whereIn('uuid', $request->product_uuids)
                ->get()
                ->keyBy('uuid');

            $productIds = [];
            $quantities = [];
            $uuidToIdMap = [];
            
            foreach ($request->product_uuids as $index => $uuid) {
                $product = $products->get($uuid);
                if ($product) {
                    $productIds[] = $product->id;
                    $quantities[] = isset($request->quantities[$index]) 
                        ? (int)$request->quantities[$index] 
                        : 1;
                    $uuidToIdMap[$product->id] = $uuid;
                }
            }

            if (empty($productIds)) {
                return $this->jsonError('No valid products found.', 'NO_PRODUCTS', [
                    'results' => [
                        'success' => [],
                        'failed' => []
                    ]
                ], 400);
            }

            $cartIdentifier = $this->cartService->getCartIdentifier();
            $results = $this->cartService->addMultipleToCart(
                $productIds,
                $quantities,
                $cartIdentifier
            );

            // Add UUIDs to results
            foreach ($results['success'] as &$item) {
                if (isset($uuidToIdMap[$item['product_id']])) {
                    $item['uuid'] = $uuidToIdMap[$item['product_id']];
                }
            }
            foreach ($results['failed'] as &$item) {
                if (isset($item['product_id']) && isset($uuidToIdMap[$item['product_id']])) {
                    $item['uuid'] = $uuidToIdMap[$item['product_id']];
                } elseif (isset($item['uuid'])) {
                    // UUID already set in service
                }
            }

            $successCount = count($results['success']);
            $failedCount = count($results['failed']);

            $message = $successCount > 0 
                ? ($failedCount > 0 
                    ? "{$successCount} item(s) added successfully. {$failedCount} item(s) failed."
                    : "{$successCount} item(s) added to cart successfully.")
                : 'Failed to add items to cart.';
            
            if ($successCount > 0) {
                return $this->jsonSuccess($message, [
                    'cart_count' => $this->cartService->getCartCount($cartIdentifier),
                    'results' => $results
                ]);
            } else {
                return $this->jsonError($message, 'CART_ADD_MULTIPLE_FAILED', [
                    'results' => [
                        'success' => [],
                        'failed' => []
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 'CART_ADD_MULTIPLE_ERROR', [
                'results' => [
                    'success' => [],
                    'failed' => []
                ]
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

            return $this->jsonSuccess('Cart updated successfully.', [
                'cart_count' => $this->cartService->getCartCount($cartIdentifier),
                'subtotal' => $cartItem->subtotal
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 'CART_UPDATE_ERROR', null, 404);
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
            $cartItem = CartItem::with(['product.category', 'product.brand'])->find($request->cart_item_id);
            
            $this->cartService->removeFromCart($request->cart_item_id, $cartIdentifier);

            return $this->jsonSuccess('Product removed from cart successfully.', [
                'cart_count' => $this->cartService->getCartCount($cartIdentifier),
                'product' => $cartItem && $cartItem->product ? [
                    'id' => $cartItem->product->id,
                    'uuid' => $cartItem->product->uuid,
                    'name' => $cartItem->product->name,
                    'category' => $cartItem->product->category->name ?? 'Uncategorized',
                    'brand' => $cartItem->product->brand->name ?? '',
                    'price' => $cartItem->product->price,
                    'quantity' => $cartItem->quantity
                ] : null
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 'CART_REMOVE_ERROR', null, 404);
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
        // Calculate total using PriceCalculationService
        $priceService = app(\App\Services\PriceCalculationService::class);
        $total = $priceService->calculateTotal($subtotal, 0, $shipping);

        return view('frontend.cart.cart', compact('title', 'cartItems', 'subtotal', 'shipping', 'total'));
    }

    // Get cart items (AJAX)
    public function list(): JsonResponse
    {
        $cartIdentifier = $this->cartService->getCartIdentifier();
        $cartItems = $this->cartService->getCartItems($cartIdentifier);

        // Use API Resource for consistent response format
        $cartItemsArray = CartItemResource::collection($cartItems)->resolve();
        // Calculate total using PriceCalculationService
        $priceService = app(\App\Services\PriceCalculationService::class);
        $total = $priceService->calculateSubtotal($cartItems);

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

        return $this->jsonSuccess('Cart count retrieved.', ['count' => $count]);
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

        // Calculate total using PriceCalculationService
        $priceService = app(\App\Services\PriceCalculationService::class);
        $total = $priceService->calculateSubtotal($cartItems);

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
            'product_uuids' => 'required|array|min:1'
        ], [
            'product_uuids.required' => 'Product UUIDs are required.',
            'product_uuids.array' => 'Product UUIDs must be an array.',
            'product_uuids.min' => 'At least one product UUID is required.'
        ]);

        // Filter and validate UUIDs
        $validUuids = collect($request->product_uuids)
            ->filter(function($uuid) {
                return !empty($uuid) && is_string($uuid) && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid);
            })
            ->unique()
            ->values()
            ->toArray();

        if (empty($validUuids)) {
            return response()->json([
                'success' => true,
                'status' => []
            ]);
        }

        // Convert UUIDs to product IDs
        $products = Product::whereIn('uuid', $validUuids)->pluck('id', 'uuid');
        $productIds = $products->values()->toArray();

        $cartIdentifier = $this->cartService->getCartIdentifier();
        $statusById = $this->cartService->checkProductsInCart(
            $cartIdentifier,
            $productIds
        );

        // Convert status back to UUID keys
        $status = [];
        foreach ($products as $uuid => $id) {
            $status[$uuid] = $statusById[$id] ?? false;
        }

        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }
}
