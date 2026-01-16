<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use App\Services\WishlistService;
use App\Http\Requests\AddToWishlistRequest;
use App\Http\Requests\RemoveMultipleFromWishlistRequest;
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
            $product = Product::where('uuid', $request->product_uuid)->firstOrFail();
            
            $this->wishlistService->addToWishlist(Auth::id(), $product->id);

            $product->load(['category', 'brand']);

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist successfully.',
                'wishlist_count' => $this->wishlistService->getWishlistCount(Auth::id()),
                'product' => $product ? [
                    'id' => $product->id,
                    'uuid' => $product->uuid,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'brand' => $product->brand->name ?? '',
                    'price' => $product->price
                ] : null
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
            'product_uuid' => 'required|uuid|exists:products,uuid'
        ]);

        try {
            $product = Product::where('uuid', $request->product_uuid)->firstOrFail();
            $product->load(['category', 'brand']);
            
            $this->wishlistService->removeFromWishlist(Auth::id(), $product->id);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist successfully.',
                'wishlist_count' => $this->wishlistService->getWishlistCount(Auth::id()),
                'product' => $product ? [
                    'id' => $product->id,
                    'uuid' => $product->uuid,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'brand' => $product->brand->name ?? '',
                    'price' => $product->price
                ] : null
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

        $statusById = $this->wishlistService->checkProductsInWishlist(
            Auth::id(),
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
            // Return empty wishlist for unauthenticated users (no 401 error)
            return response()->json([
                'success' => true,
                'html' => '',
                'count' => 0,
                'message' => 'Please login to view wishlist.'
            ]);
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

    // Remove multiple products from wishlist (batch operation)
    public function removeMultiple(RemoveMultipleFromWishlistRequest $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to remove items from wishlist.',
                'requires_login' => true
            ], 401);
        }

        try {
            // Get products with UUIDs
            $products = Product::whereIn('uuid', $request->product_uuids)
                ->get()
                ->keyBy('uuid');

            $productIds = [];
            $uuidToIdMap = [];
            
            foreach ($request->product_uuids as $uuid) {
                $product = $products->get($uuid);
                if ($product) {
                    $productIds[] = $product->id;
                    $uuidToIdMap[$product->id] = $uuid;
                }
            }

            if (empty($productIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid products found.',
                    'results' => [
                        'success' => [],
                        'failed' => []
                    ]
                ], 400);
            }

            $results = $this->wishlistService->removeMultipleFromWishlist(
                Auth::id(),
                $productIds
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
                }
            }

            $successCount = count($results['success']);
            $failedCount = count($results['failed']);

            return response()->json([
                'success' => $successCount > 0,
                'message' => $successCount > 0 
                    ? ($failedCount > 0 
                        ? "{$successCount} item(s) removed successfully. {$failedCount} item(s) failed."
                        : "{$successCount} item(s) removed from wishlist successfully.")
                    : 'Failed to remove items from wishlist.',
                'wishlist_count' => $this->wishlistService->getWishlistCount(Auth::id()),
                'results' => $results
            ], $successCount > 0 ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'results' => [
                    'success' => [],
                    'failed' => []
                ]
            ], 400);
        }
    }
}
