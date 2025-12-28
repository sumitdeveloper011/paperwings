<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use App\Services\WishlistService;
use App\Http\Requests\AddToWishlistRequest;
use App\Http\Resources\WishlistItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function __construct(
        private WishlistService $wishlistService
    ) {}

    // Add product to wishlist
    public function add(AddToWishlistRequest $request): JsonResponse
    {
        try {
            $this->wishlistService->addToWishlist(Auth::id(), $request->product_id);

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist successfully.',
                'wishlist_count' => $this->wishlistService->getWishlistCount(Auth::id())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Remove product from wishlist
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

        try {
            $this->wishlistService->removeFromWishlist(Auth::id(), $request->product_id);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist successfully.',
                'wishlist_count' => $this->wishlistService->getWishlistCount(Auth::id())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    // Get wishlist items
    public function list(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to view wishlist.',
                'requires_login' => true
            ], 401);
        }

        // Use API Resource for consistent response format
        $wishlistItems = $this->wishlistService->getWishlistItems(Auth::id());
        $itemsArray = WishlistItemResource::collection($wishlistItems)
            ->collection
            ->filter(fn($item) => !isset($item['error']))
            ->values()
            ->toArray();

        return response()->json([
            'success' => true,
            'items' => $itemsArray,
            'count' => count($itemsArray)
        ]);
    }

    // Check if product(s) is in wishlist
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

        $status = $this->wishlistService->checkProductsInWishlist(
            Auth::id(),
            $request->product_ids
        );

        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }

    // Get wishlist count
    public function count(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'count' => 0
            ]);
        }

        $count = $this->wishlistService->getWishlistCount(Auth::id());

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    // Render wishlist items as HTML (for AJAX)
    public function render(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'html' => '',
                'count' => 0,
                'message' => 'Please login to view wishlist.',
                'requires_login' => true
            ], 401);
        }

        $wishlistItems = $this->wishlistService->getWishlistItems(Auth::id())
            ->filter(function($item) {
                return $item->product !== null;
            });

        $html = view('frontend.wishlist.partials.items', [
            'items' => $wishlistItems
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'count' => $wishlistItems->count()
        ]);
    }
}
