<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    /**
     * Add product to wishlist
     */
    public function add(Request $request): JsonResponse
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add items to wishlist.',
                'requires_login' => true,
                'redirect_url' => route('login')
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        // Check if product already exists in wishlist
        $existingWishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingWishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist.'
            ], 400);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);

        // Get updated wishlist count
        $wishlistCount = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully.',
            'wishlist_count' => $wishlistCount
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function remove(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to remove items from wishlist.',
                'requires_login' => true
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            // Get updated wishlist count
            $wishlistCount = Wishlist::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist successfully.',
                'wishlist_count' => $wishlistCount
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in wishlist.'
        ], 404);
    }

    /**
     * Get wishlist items
     */
    public function list(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to view wishlist.',
                'requires_login' => true
            ], 401);
        }

        $userId = Auth::id();

        $wishlistItems = Wishlist::where('wishlists.user_id', $userId)
            ->join('products', 'wishlists.product_id', '=', 'products.id')
            ->select(
                'wishlists.id',
                'wishlists.product_id',
                'products.name as product_name',
                'products.slug as product_slug',
                'products.total_price as product_price',
                'products.discount_price',
                DB::raw('(SELECT image FROM products_images WHERE product_id = products.id ORDER BY id ASC LIMIT 1) as product_image_path')
            )
            ->get()
            ->map(function ($item) {
                // Format image URL similar to ProductImage accessor
                $productImage = $item->product_image_path
                    ? asset('storage/' . $item->product_image_path)
                    : asset('assets/images/placeholder.jpg');

                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_slug' => $item->product_slug,
                    'product_price' => $item->product_price,
                    'discount_price' => $item->discount_price,
                    'product_image' => $productImage,
                    'product_url' => route('product.detail', $item->product_slug),
                ];
            });

        return response()->json([
            'success' => true,
            'items' => $wishlistItems,
            'count' => $wishlistItems->count()
        ]);
    }

    /**
     * Check if product(s) is in wishlist
     */
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

        $userId = Auth::id();
        $productIds = $request->product_ids;

        $wishlistItems = Wishlist::where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $status = [];
        foreach ($productIds as $productId) {
            $status[$productId] = in_array($productId, $wishlistItems);
        }

        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }

    /**
     * Get wishlist count
     */
    public function count(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'count' => 0
            ]);
        }

        $userId = Auth::id();
        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
