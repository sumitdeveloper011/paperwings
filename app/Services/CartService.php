<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private CartItem $cartItem,
        private Product $product
    ) {}

    // Get cart identifier (user_id only - authentication required)
    public function getCartIdentifier(): array
    {
        $userId = auth()->id();
        if (!$userId) {
            throw new \Exception('User must be authenticated to access cart.');
        }
        return ['user_id' => $userId];
    }

    // Add product to cart
    public function addToCart(int $productId, int $quantity, array $cartIdentifier): CartItem
    {
        return DB::transaction(function () use ($productId, $quantity, $cartIdentifier) {
            // Lock product row to prevent race conditions
            $product = $this->product->lockForUpdate()->findOrFail($productId);
            
            if (!$product->status) {
                \Illuminate\Support\Facades\Log::warning('Attempted to add inactive product to cart', [
                    'user_id' => $cartIdentifier['user_id'],
                    'product_id' => $productId,
                ]);
                throw new \Exception('This product is not available.');
            }

            // Check stock availability
            if ($product->stock < $quantity) {
                $availableStock = max(0, $product->stock);
                \Illuminate\Support\Facades\Log::warning('Insufficient stock when adding to cart', [
                    'user_id' => $cartIdentifier['user_id'],
                    'product_id' => $productId,
                    'requested_quantity' => $quantity,
                    'available_stock' => $availableStock,
                ]);
                throw new \Exception(
                    $availableStock > 0 
                        ? "Only {$availableStock} item(s) available in stock." 
                        : 'This product is out of stock.'
                );
            }

            $price = $product->discount_price ?? $product->total_price;
            
            if (!$price) {
                throw new \Exception('Product price is not set.');
            }
            
            // Check existing cart item
            $existingItem = $this->cartItem->where('product_id', $productId)
                ->where('user_id', $cartIdentifier['user_id'])
                ->first();
            
            if ($existingItem) {
                // Check if total quantity (existing + new) exceeds stock
                $totalQuantity = $existingItem->quantity + $quantity;
                if ($product->stock < $totalQuantity) {
                    $maxAllowed = $product->stock - $existingItem->quantity;
                    throw new \Exception(
                        $maxAllowed > 0
                            ? "You can only add {$maxAllowed} more item(s). Only {$product->stock} available in stock."
                            : 'Maximum available quantity already in cart.'
                    );
                }

                // Update quantity atomically
                $existingItem->increment('quantity', $quantity);
                $existingItem->price = $price; // Update price in case it changed
                $existingItem->save();
                
                // Remove product from wishlist if it exists
                Wishlist::where('user_id', $cartIdentifier['user_id'])
                    ->where('product_id', $productId)
                    ->delete();
                
                return $existingItem;
            }
            
            // Create new cart item
            $cartItem = $this->cartItem->create([
                'product_id' => $productId,
                'user_id' => $cartIdentifier['user_id'],
                'quantity' => $quantity,
                'price' => $price,
            ]);

            // Remove product from wishlist if it exists
            Wishlist::where('user_id', $cartIdentifier['user_id'])
                ->where('product_id', $productId)
                ->delete();

            return $cartItem;
        });
    }

    // Update cart item quantity
    public function updateCartItem(int $cartItemId, int $quantity, array $cartIdentifier): CartItem
    {
        return DB::transaction(function () use ($cartItemId, $quantity, $cartIdentifier) {
            $cartItem = $this->getCartItem($cartItemId, $cartIdentifier);
            
            if (!$cartItem) {
                throw new \Exception('Cart item not found.');
            }

            // Lock product row to check stock
            $product = $this->product->lockForUpdate()->findOrFail($cartItem->product_id);
            
            if (!$product->status) {
                throw new \Exception('This product is no longer available.');
            }

            // Check stock availability
            if ($product->stock < $quantity) {
                $availableStock = max(0, $product->stock);
                throw new \Exception(
                    $availableStock > 0 
                        ? "Only {$availableStock} item(s) available in stock." 
                        : 'This product is out of stock.'
                );
            }

            // Update price in case it changed
            $price = $product->discount_price ?? $product->total_price;
            if (!$price) {
                throw new \Exception('Product price is not set.');
            }

            $cartItem->quantity = $quantity;
            $cartItem->price = $price;
            $cartItem->save();

            return $cartItem;
        });
    }

    // Remove item from cart
    public function removeFromCart(int $cartItemId, array $cartIdentifier): bool
    {
        $cartItem = $this->getCartItem($cartItemId, $cartIdentifier);
        
        if (!$cartItem) {
            throw new \Exception('Cart item not found.');
        }

        return $cartItem->delete();
    }

    // Get cart items with products
    public function getCartItems(array $cartIdentifier): Collection
    {
        return $this->cartItem
            ->with(['product' => function($query) {
                $query->select('id', 'uuid', 'name', 'slug', 'total_price', 'discount_price', 'barcode', 'status', 'category_id', 'brand_id')
                      ->with([
                          'images' => function($q) {
                              $q->select('id', 'product_id', 'image')
                                ->orderBy('id')
                                ->limit(1);
                          },
                          'category:id,name,slug',
                          'brand:id,name'
                      ]);
            }])
            ->where('user_id', $cartIdentifier['user_id'])
            ->get();
    }

    // Get cart count (total quantity)
    public function getCartCount(array $cartIdentifier): int
    {
        return $this->cartItem
            ->where('user_id', $cartIdentifier['user_id'])
            ->sum('quantity');
    }

    // Get cart subtotal
    public function getCartSubtotal(array $cartIdentifier): float
    {
        return $this->cartItem
            ->where('user_id', $cartIdentifier['user_id'])
            ->get()
            ->sum(fn($item) => $item->subtotal);
    }

    // Get a specific cart item
    private function getCartItem(int $cartItemId, array $cartIdentifier): ?CartItem
    {
        return $this->cartItem
            ->where('id', $cartItemId)
            ->where('user_id', $cartIdentifier['user_id'])
            ->first();
    }

    // Clear cart
    public function clearCart(array $cartIdentifier): int
    {
        return $this->cartItem
            ->where('user_id', $cartIdentifier['user_id'])
            ->delete();
    }

    // Check if products are in cart
    public function checkProductsInCart(array $cartIdentifier, array $productIds): array
    {
        $cartProductIds = $this->cartItem
            ->where('user_id', $cartIdentifier['user_id'])
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $status = [];
        foreach ($productIds as $productId) {
            $status[$productId] = in_array($productId, $cartProductIds);
        }

        return $status;
    }

    // Add multiple products to cart (batch operation)
    public function addMultipleToCart(array $productIds, array $quantities, array $cartIdentifier): array
    {
        return DB::transaction(function () use ($productIds, $quantities, $cartIdentifier) {
            $results = [
                'success' => [],
                'failed' => []
            ];

            // Get all products at once with lock
            $products = $this->product->lockForUpdate()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            // Get existing cart items
            $existingItems = $this->cartItem
                ->where('user_id', $cartIdentifier['user_id'])
                ->whereIn('product_id', $productIds)
                ->get()
                ->keyBy('product_id');

            foreach ($productIds as $index => $productId) {
                try {
                    $product = $products->get($productId);
                    
                    if (!$product) {
                        $results['failed'][] = [
                            'product_id' => $productId,
                            'uuid' => null,
                            'message' => 'Product not found.'
                        ];
                        continue;
                    }

                    if (!$product->status) {
                        $results['failed'][] = [
                            'product_id' => $productId,
                            'uuid' => $product->uuid ?? null,
                            'message' => 'This product is not available.'
                        ];
                        continue;
                    }

                    $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : 1;
                    
                    if ($quantity < 1) {
                        $quantity = 1;
                    }

                    // Check stock availability
                    $existingItem = $existingItems->get($productId);
                    $requestedQuantity = $existingItem ? ($existingItem->quantity + $quantity) : $quantity;

                    if ($product->stock < $requestedQuantity) {
                        $availableStock = max(0, $product->stock);
                        $maxAllowed = $existingItem ? ($product->stock - $existingItem->quantity) : $product->stock;
                        
                        $results['failed'][] = [
                            'product_id' => $productId,
                            'uuid' => $product->uuid,
                            'message' => $availableStock > 0 
                                ? ($maxAllowed > 0 
                                    ? "You can only add {$maxAllowed} more item(s). Only {$product->stock} available in stock."
                                    : 'Maximum available quantity already in cart.')
                                : 'This product is out of stock.'
                        ];
                        continue;
                    }

                    $price = $product->discount_price ?? $product->total_price;
                    
                    if (!$price) {
                        $results['failed'][] = [
                            'product_id' => $productId,
                            'uuid' => $product->uuid,
                            'message' => 'Product price is not set.'
                        ];
                        continue;
                    }

                    // Add or update cart item
                    if ($existingItem) {
                        $existingItem->increment('quantity', $quantity);
                        $existingItem->price = $price;
                        $existingItem->save();
                        $cartItem = $existingItem;
                    } else {
                        $cartItem = $this->cartItem->create([
                            'product_id' => $productId,
                            'user_id' => $cartIdentifier['user_id'],
                            'quantity' => $quantity,
                            'price' => $price,
                        ]);
                    }

                    // Remove from wishlist if exists
                    Wishlist::where('user_id', $cartIdentifier['user_id'])
                        ->where('product_id', $productId)
                        ->delete();

                    $results['success'][] = [
                        'product_id' => $productId,
                        'uuid' => $product->uuid,
                        'name' => $product->name,
                        'quantity' => $quantity,
                        'cart_item_id' => $cartItem->id
                    ];

                } catch (\Exception $e) {
                    $product = $products->get($productId);
                    $results['failed'][] = [
                        'product_id' => $productId,
                        'uuid' => $product ? $product->uuid : null,
                        'message' => $e->getMessage()
                    ];
                }
            }

            return $results;
        });
    }
}

