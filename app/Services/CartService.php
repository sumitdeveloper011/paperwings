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
        return ['user_id' => auth()->id()];
    }

    // Add product to cart
    public function addToCart(int $productId, int $quantity, array $cartIdentifier): CartItem
    {
        $product = $this->product->findOrFail($productId);
        
        if (!$product->status) {
            throw new \Exception('This product is not available.');
        }

        $price = $product->discount_price ?? $product->total_price;
        
        if (!$price) {
            throw new \Exception('Product price is not set.');
        }
        
        // Use updateOrCreate for atomic operation (works on both MySQL and SQLite)
        $existingItem = $this->cartItem->where('product_id', $productId)
            ->where('user_id', $cartIdentifier['user_id'])
            ->first();
        
        if ($existingItem) {
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
    }

    // Update cart item quantity
    public function updateCartItem(int $cartItemId, int $quantity, array $cartIdentifier): CartItem
    {
        $cartItem = $this->getCartItem($cartItemId, $cartIdentifier);
        
        if (!$cartItem) {
            throw new \Exception('Cart item not found.');
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return $cartItem;
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
                $query->select('id', 'name', 'slug', 'total_price', 'discount_price', 'barcode', 'status')
                      ->with(['images' => function($q) {
                          $q->select('id', 'product_id', 'image')
                            ->orderBy('id')
                            ->limit(1);
                      }]);
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
}

