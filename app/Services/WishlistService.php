<?php

namespace App\Services;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class WishlistService
{
    public function __construct(
        private Wishlist $wishlist,
        private Product $product
    ) {}

    // Add product to wishlist
    public function addToWishlist(int $userId, int $productId): Wishlist
    {
        $product = $this->product->findOrFail($productId);
        
        if (!$product->status) {
            throw new \Exception('This product is not available.');
        }

        $existing = $this->wishlist
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            throw new \Exception('Product is already in your wishlist.');
        }

        return $this->wishlist->create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
    }

    // Remove product from wishlist
    public function removeFromWishlist(int $userId, int $productId): bool
    {
        $wishlistItem = $this->wishlist
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if (!$wishlistItem) {
            throw new \Exception('Product not found in wishlist.');
        }

        return $wishlistItem->delete();
    }

    // Get wishlist items with products
    public function getWishlistItems(int $userId): Collection
    {
        return $this->wishlist
            ->where('user_id', $userId)
            ->with([
                'product' => function($query) {
                    $query->select('id', 'name', 'slug', 'total_price', 'discount_price', 'status')
                          ->with(['images' => function($q) {
                              $q->select('id', 'product_id', 'image')
                                ->orderBy('id')
                                ->limit(1);
                          }]);
                }
            ])
            ->get();
    }

    // Get wishlist count
    public function getWishlistCount(int $userId): int
    {
        return $this->wishlist->where('user_id', $userId)->count();
    }

    // Check if products are in wishlist
    public function checkProductsInWishlist(int $userId, array $productIds): array
    {
        $wishlistProductIds = $this->wishlist
            ->where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $status = [];
        foreach ($productIds as $productId) {
            $status[$productId] = in_array($productId, $wishlistProductIds);
        }

        return $status;
    }

    // Check if single product is in wishlist
    public function isInWishlist(int $userId, int $productId): bool
    {
        return $this->wishlist
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }
}

