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

            $product->load(['category', 'brand', 'images' => function($query) {
                $query->select('id', 'product_id', 'image')->orderBy('id')->limit(1);
            }]);

            $price = $product->discount_price ?? $product->total_price;

            return $this->jsonSuccess('Product added to wishlist successfully.', [
                'wishlist_count' => $this->wishlistService->getWishlistCount(Auth::id()),
                'product' => $product ? [
                    'id' => $product->id,
                    'uuid' => $product->uuid,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'brand' => $product->brand->name ?? '',
                    'price' => $price,
                    'total_price' => $product->total_price,
                    'discount_price' => $product->discount_price,
                    'image_url' => $product->images->isNotEmpty() ? $product->images->first()->thumbnail_url : asset('assets/images/placeholder.jpg'),
                    'image_alt' => $product->name
                ] : null
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 'WISHLIST_ADD_ERROR', null, 400);
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

            return $this->jsonSuccess('Product removed from wishlist successfully.', [
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
            return $this->jsonError($e->getMessage(), 'WISHLIST_REMOVE_ERROR', null, 404);
        }
    }

    // Get wishlist items
    public function list(): JsonResponse
    {
        if (!Auth::check()) {
            return $this->jsonError('Please login to view wishlist.', 'UNAUTHENTICATED', ['requires_login' => true], 401);
        }

        // Use API Resource for consistent response format
        $wishlistItems = $this->wishlistService->getWishlistItems(Auth::id());
        $itemsArray = WishlistItemResource::collection($wishlistItems)
            ->collection
            ->filter(fn($item) => !isset($item['error']))
            ->values()
            ->toArray();

        return $this->jsonSuccess('Wishlist items retrieved.', [
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

        return $this->jsonSuccess('Wishlist status checked.', ['status' => $status]);
    }

    // Get wishlist count
    public function count(): JsonResponse
    {
        try {
            if (!Auth::check()) {
                return $this->jsonSuccess('Wishlist count retrieved.', ['count' => 0]);
            }

            $userId = Auth::id();
            if (!$userId) {
                return $this->jsonError('User ID not found.', 'USER_ID_MISSING', null, 400);
            }

            $count = $this->wishlistService->getWishlistCount($userId);

            return $this->jsonSuccess('Wishlist count retrieved.', ['count' => $count]);
        } catch (\Exception $e) {
            return $this->jsonError('An error occurred while retrieving wishlist count.', 'INTERNAL_SERVER_ERROR', null, 500);
        }
    }

    // Render wishlist items as HTML (for AJAX)
    public function render(): JsonResponse
    {
        if (!Auth::check()) {
            // Return empty wishlist for unauthenticated users (no 401 error)
            return $this->jsonSuccess('Please login to view wishlist.', [
                'html' => '',
                'count' => 0
            ]);
        }

        $wishlistItems = $this->wishlistService->getWishlistItems(Auth::id())
            ->filter(function($item) {
                return $item->product !== null;
            });

        $html = view('frontend.wishlist.partials.items', [
            'items' => $wishlistItems
        ])->render();

        return $this->jsonSuccess('Wishlist rendered.', [
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
                return $this->jsonError('No valid products found.', 'NO_PRODUCTS', [
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

            $message = $successCount > 0 
                ? ($failedCount > 0 
                    ? "{$successCount} item(s) removed successfully. {$failedCount} item(s) failed."
                    : "{$successCount} item(s) removed from wishlist successfully.")
                : 'Failed to remove items from wishlist.';

            if ($successCount > 0) {
                return $this->jsonSuccess($message, [
                    'wishlist_count' => $this->wishlistService->getWishlistCount(Auth::id()),
                    'results' => $results
                ]);
            } else {
                return $this->jsonError($message, 'WISHLIST_REMOVE_MULTIPLE_FAILED', [
                    'results' => [
                        'success' => [],
                        'failed' => []
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage(), 'WISHLIST_REMOVE_MULTIPLE_ERROR', [
                'results' => [
                    'success' => [],
                    'failed' => []
                ]
            ], 400);
        }
    }
}
