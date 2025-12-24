<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private CartItem $cartItem,
        private Product $product
    ) {}

    /**
     * Get cart identifier (user_id or session_id)
     */
    public function getCartIdentifier(): array
    {
        if (auth()->check()) {
            return ['user_id' => auth()->id(), 'session_id' => null];
        }

        return ['user_id' => null, 'session_id' => session()->getId()];
    }

    /**
     * Add product to cart
     *
     * @param int $productId
     * @param int $quantity
     * @param array $cartIdentifier
     * @return CartItem
     * @throws \Exception
     */
    public function addToCart(int $productId, int $quantity, array $cartIdentifier): CartItem
    {
        $product = $this->product->findOrFail($productId);
        
        if (!$product->status) {
            throw new \Exception('This product is not available.');
        }

        $price = $product->discount_price ?? $product->total_price;
        
        // Use updateOrCreate with increment for better performance
        return $this->cartItem->updateOrCreate(
            [
                'product_id' => $productId,
                'user_id' => $cartIdentifier['user_id'],
                'session_id' => $cartIdentifier['session_id'],
            ],
            [
                'quantity' => DB::raw("COALESCE(quantity, 0) + {$quantity}"),
                'price' => $price,
            ]
        );
    }

    /**
     * Update cart item quantity
     *
     * @param int $cartItemId
     * @param int $quantity
     * @param array $cartIdentifier
     * @return CartItem
     * @throws \Exception
     */
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

    /**
     * Remove item from cart
     *
     * @param int $cartItemId
     * @param array $cartIdentifier
     * @return bool
     * @throws \Exception
     */
    public function removeFromCart(int $cartItemId, array $cartIdentifier): bool
    {
        $cartItem = $this->getCartItem($cartItemId, $cartIdentifier);
        
        if (!$cartItem) {
            throw new \Exception('Cart item not found.');
        }

        return $cartItem->delete();
    }

    /**
     * Get cart items with products
     *
     * @param array $cartIdentifier
     * @return Collection
     */
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
            ->when($cartIdentifier['user_id'], fn($q) => $q->where('user_id', $cartIdentifier['user_id']))
            ->when($cartIdentifier['session_id'], fn($q) => $q->where('session_id', $cartIdentifier['session_id']))
            ->get();
    }

    /**
     * Get cart count (total quantity)
     *
     * @param array $cartIdentifier
     * @return int
     */
    public function getCartCount(array $cartIdentifier): int
    {
        return $this->cartItem
            ->when($cartIdentifier['user_id'], fn($q) => $q->where('user_id', $cartIdentifier['user_id']))
            ->when($cartIdentifier['session_id'], fn($q) => $q->where('session_id', $cartIdentifier['session_id']))
            ->sum('quantity');
    }

    /**
     * Get cart subtotal
     *
     * @param array $cartIdentifier
     * @return float
     */
    public function getCartSubtotal(array $cartIdentifier): float
    {
        return $this->cartItem
            ->when($cartIdentifier['user_id'], fn($q) => $q->where('user_id', $cartIdentifier['user_id']))
            ->when($cartIdentifier['session_id'], fn($q) => $q->where('session_id', $cartIdentifier['session_id']))
            ->get()
            ->sum(fn($item) => $item->subtotal);
    }

    /**
     * Get a specific cart item
     *
     * @param int $cartItemId
     * @param array $cartIdentifier
     * @return CartItem|null
     */
    private function getCartItem(int $cartItemId, array $cartIdentifier): ?CartItem
    {
        return $this->cartItem
            ->where('id', $cartItemId)
            ->when($cartIdentifier['user_id'], fn($q) => $q->where('user_id', $cartIdentifier['user_id']))
            ->when($cartIdentifier['session_id'], fn($q) => $q->where('session_id', $cartIdentifier['session_id']))
            ->first();
    }

    /**
     * Clear cart
     *
     * @param array $cartIdentifier
     * @return int Number of items deleted
     */
    public function clearCart(array $cartIdentifier): int
    {
        return $this->cartItem
            ->when($cartIdentifier['user_id'], fn($q) => $q->where('user_id', $cartIdentifier['user_id']))
            ->when($cartIdentifier['session_id'], fn($q) => $q->where('session_id', $cartIdentifier['session_id']))
            ->delete();
    }

    /**
     * Merge session cart with user cart (when user logs in)
     *
     * @param string $sessionId
     * @param int $userId
     * @return void
     */
    public function mergeCarts(string $sessionId, int $userId): void
    {
        $sessionCartItems = $this->cartItem->where('session_id', $sessionId)->get();
        
        foreach ($sessionCartItems as $sessionItem) {
            $existingItem = $this->cartItem
                ->where('user_id', $userId)
                ->where('product_id', $sessionItem->product_id)
                ->first();

            if ($existingItem) {
                // Merge quantities
                $existingItem->quantity += $sessionItem->quantity;
                $existingItem->save();
                $sessionItem->delete();
            } else {
                // Transfer to user cart
                $sessionItem->user_id = $userId;
                $sessionItem->session_id = null;
                $sessionItem->save();
            }
        }
    }
}

