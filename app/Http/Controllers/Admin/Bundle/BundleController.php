<?php

namespace App\Http\Controllers\Admin\Bundle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bundle\StoreBundleRequest;
use App\Http\Requests\Admin\Bundle\UpdateBundleRequest;
use App\Models\BundleImage;
use App\Models\ProductBundle;
use App\Models\Product;
use App\Repositories\Interfaces\BundleRepositoryInterface;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BundleController extends Controller
{
    protected BundleRepositoryInterface $bundleRepository;
    protected ImageService $imageService;

    public function __construct(
        BundleRepositoryInterface $bundleRepository,
        ImageService $imageService
    ) {
        $this->bundleRepository = $bundleRepository;
        $this->imageService = $imageService;
    }
    public function index(Request $request): View|JsonResponse
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status');

        // Build query using repository pattern
        $query = ProductBundle::withCount('products')->with('images');

        // Apply search filter
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        // Get paginated results with query parameters preserved
        $bundles = $query->ordered()
            ->paginate(15)
            ->withPath($request->url())
            ->appends($request->except('page'));

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($bundles->hasPages()) {
                $paginationHtml = view('components.pagination', ['paginator' => $bundles])->render();
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.bundle.partials.table', ['bundles' => $bundles])->render(),
                'pagination' => $paginationHtml,
                'total' => $bundles->total()
            ]);
        }

        return view('admin.bundle.index', compact('bundles', 'search', 'status'));
    }

    public function create(): View
    {
        $categories = \App\Models\Category::active()->ordered()->get();

        // If validation failed, load products from old input
        $oldProductIds = old('product_ids', []);
        $oldQuantities = old('quantities', []);
        $oldProducts = collect();

        if (!empty($oldProductIds)) {
            // Get products and maintain the order from oldProductIds
            $productsById = \App\Models\Product::whereIn('id', $oldProductIds)
                ->get()
                ->keyBy('id');

            // Build collection in the same order as oldProductIds
            $oldProducts = collect($oldProductIds)->map(function($productId, $index) use ($productsById, $oldQuantities) {
                if (!$productsById->has($productId)) {
                    return null;
                }
                $product = $productsById->get($productId);
                $quantity = isset($oldQuantities[$index]) ? $oldQuantities[$index] : 1;
                // Create a fake pivot relationship for quantity
                $product->setRelation('pivot', (object)['quantity' => $quantity]);
                return $product;
            })->filter(); // Remove nulls if any product not found
        }

        return view('admin.bundle.create', compact('categories', 'oldProducts', 'oldProductIds', 'oldQuantities'));
    }

    public function store(StoreBundleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Generate UUID for bundle
        $bundleUuid = Str::uuid()->toString();

        $bundle = $this->bundleRepository->create([
            'uuid' => $bundleUuid,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'bundle_price' => $validated['bundle_price'],
            'discount_percentage' => $validated['discount_percentage'] ?? null,
            'status' => $validated['status'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        // Upload images using ImageService
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->imageService->uploadImage($image, 'bundles', $bundleUuid);
                if ($imagePath) {
                    // Check if image already exists for this bundle to prevent duplicates
                    $existingImage = BundleImage::where('bundle_id', $bundle->id)
                        ->where('image', $imagePath)
                        ->first();

                    if (!$existingImage) {
                        BundleImage::create([
                            'uuid' => Str::uuid(),
                            'bundle_id' => $bundle->id,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }
        }

        // Attach products - remove duplicates and prepare sync data
        $productsData = [];
        $productIds = array_unique($validated['product_ids']); // Remove duplicates

        foreach ($productIds as $index => $productId) {
            // Find the quantity for this product (handle array index mismatch after removing duplicates)
            $originalIndex = array_search($productId, $validated['product_ids']);
            $quantity = isset($validated['quantities'][$originalIndex])
                ? $validated['quantities'][$originalIndex]
                : 1;

            $productsData[$productId] = [
                'quantity' => max(1, (int)$quantity) // Ensure quantity is at least 1
            ];
        }

        // Use sync to attach products (handles duplicates automatically)
        $bundle->products()->sync($productsData);

        // Auto-calculate discount percentage based on total products price vs bundle price
        $bundle->load('products');
        $totalProductsPrice = $bundle->products->sum(function($product) {
            return ($product->discount_price ?? $product->total_price) * ($product->pivot->quantity ?? 1);
        });

        $discountPercentage = 0;
        if ($totalProductsPrice > 0 && $bundle->bundle_price > 0) {
            $discountPercentage = (($totalProductsPrice - $bundle->bundle_price) / $totalProductsPrice) * 100;
            $discountPercentage = max(0, min(100, $discountPercentage)); // Clamp between 0-100
        }

        // Update discount percentage
        $bundle->update([
            'discount_percentage' => round($discountPercentage, 2)
        ]);

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle created successfully!');
    }

    public function show($bundle): View
    {
        // Find bundle including soft deleted ones using UUID
        $bundle = $this->bundleRepository->findByUuidWithTrashed($bundle);

        if (!$bundle) {
            abort(404);
        }

        $bundle->load(['products.images', 'images']);
        return view('admin.bundle.show', compact('bundle'));
    }

    public function edit($bundle): View|RedirectResponse
    {
        // Find bundle including soft deleted ones using UUID
        $bundle = ProductBundle::withTrashed()->where('uuid', $bundle)->firstOrFail();

        // Don't allow editing trashed bundles
        if ($bundle->trashed()) {
            return redirect()->route('admin.bundles.trash')
                ->with('error', 'Cannot edit a deleted bundle. Please restore it first.');
        }

        $bundle->load(['products', 'images']);
        $categories = \App\Models\Category::active()->ordered()->get();

        // If validation failed, load products from old input
        $oldProductIds = old('product_ids', []);
        $oldQuantities = old('quantities', []);
        $oldProducts = collect();

        if (!empty($oldProductIds)) {
            // Get products and maintain the order from oldProductIds
            $productsById = \App\Models\Product::whereIn('id', $oldProductIds)
                ->get()
                ->keyBy('id');

            // Build collection in the same order as oldProductIds
            $oldProducts = collect($oldProductIds)->map(function($productId, $index) use ($productsById, $oldQuantities) {
                if (!$productsById->has($productId)) {
                    return null;
                }
                $product = $productsById->get($productId);
                $quantity = isset($oldQuantities[$index]) ? $oldQuantities[$index] : 1;
                // Create a fake pivot relationship for quantity
                $product->setRelation('pivot', (object)['quantity' => $quantity]);
                return $product;
            })->filter(); // Remove nulls if any product not found
        }

        return view('admin.bundle.edit', compact('bundle', 'categories', 'oldProducts', 'oldProductIds', 'oldQuantities'));
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('search', $request->get('term', ''));
        $categoryId = $request->get('category_id');
        $page = $request->get('page', 1);
        $perPage = 50;

        $query = Product::active()->with('images');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')
                          ->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

        $results = $products->map(function($product) {
            return [
                'id' => $product->id,
                'text' => $product->name . ' - $' . number_format($product->total_price, 2),
                'name' => $product->name,
                'price' => $product->total_price,
                'image' => $product->main_image,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $products->count() === $perPage
            ]
        ]);
    }

    public function update(UpdateBundleRequest $request, $bundle): RedirectResponse
    {
        // Find bundle including soft deleted ones using UUID
        $bundle = $this->bundleRepository->findByUuidWithTrashed($bundle);

        if (!$bundle) {
            abort(404);
        }

        // Don't allow updating trashed bundles
        if ($bundle->trashed()) {
            return redirect()->route('admin.bundles.trash')
                ->with('error', 'Cannot update a deleted bundle. Please restore it first.');
        }

        $validated = $request->validated();

        $this->bundleRepository->update($bundle, [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'bundle_price' => $validated['bundle_price'],
            // Discount percentage will be calculated after products are synced
            'status' => $validated['status'] ?? $bundle->status,
            'sort_order' => $validated['sort_order'] ?? $bundle->sort_order,
        ]);

        // Handle image upload - delete old images if not keeping existing
        if (!$request->boolean('keep_existing_images')) {
            $oldImages = $bundle->images;
            foreach ($oldImages as $oldImage) {
                $this->imageService->deleteImage($oldImage->image);
                $oldImage->delete();
            }
        }

        // Upload new images using ImageService
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->imageService->uploadImage($image, 'bundles', $bundle->uuid);
                if ($imagePath) {
                    // Check if image already exists for this bundle to prevent duplicates
                    $existingImage = BundleImage::where('bundle_id', $bundle->id)
                        ->where('image', $imagePath)
                        ->first();

                    if (!$existingImage) {
                        BundleImage::create([
                            'uuid' => Str::uuid(),
                            'bundle_id' => $bundle->id,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }
        }

        // Sync products - remove duplicates and prepare sync data
        $productsData = [];
        $productIds = array_unique($validated['product_ids']); // Remove duplicates

        foreach ($productIds as $index => $productId) {
            // Find the quantity for this product (handle array index mismatch after removing duplicates)
            $originalIndex = array_search($productId, $validated['product_ids']);
            $quantity = isset($validated['quantities'][$originalIndex])
                ? $validated['quantities'][$originalIndex]
                : 1;

            $productsData[$productId] = [
                'quantity' => max(1, (int)$quantity) // Ensure quantity is at least 1
            ];
        }

        // Use sync to update products (handles duplicates automatically)
        $bundle->products()->sync($productsData);

        // Auto-calculate discount percentage based on total products price vs bundle price
        $bundle->load('products');
        $totalProductsPrice = $bundle->products->sum(function($product) {
            return ($product->discount_price ?? $product->total_price) * ($product->pivot->quantity ?? 1);
        });

        $discountPercentage = 0;
        if ($totalProductsPrice > 0 && $bundle->bundle_price > 0) {
            $discountPercentage = (($totalProductsPrice - $bundle->bundle_price) / $totalProductsPrice) * 100;
            $discountPercentage = max(0, min(100, $discountPercentage)); // Clamp between 0-100
        }

        // Update discount percentage
        $bundle->update([
            'discount_percentage' => round($discountPercentage, 2)
        ]);

        // Refresh bundle to get updated data
        $bundle->refresh();

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle updated successfully!');
    }

    // Remove the specified resource from storage (Soft Delete)
    public function destroy($bundle): RedirectResponse
    {
        // Find bundle using UUID
        $bundle = $this->bundleRepository->findByUuid($bundle);

        if (!$bundle) {
            abort(404);
        }

        // Soft delete the bundle (image remains intact for restore)
        // Image will only be deleted on forceDelete (permanent delete)
        $this->bundleRepository->delete($bundle);

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle deleted successfully!');
    }

    // Show trashed (soft deleted) bundles
    public function trash(Request $request): View
    {
        $search = trim($request->get('search', ''));

        // Build query for trashed bundles
        // Get trashed bundles using repository
        $bundles = $this->bundleRepository->getTrashed(15);

        // Apply search filter if needed
        $query = ProductBundle::onlyTrashed()->withCount('products')->with('images');

        // Apply search filter
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Get paginated results
        $bundles = $query->orderBy('deleted_at', 'desc')
            ->paginate(15)
            ->withPath($request->url())
            ->appends($request->except('page'));

        return view('admin.bundle.trash', compact('bundles', 'search'));
    }

    // Restore soft deleted bundle
    public function restore($bundle): RedirectResponse
    {
        // Find bundle including soft deleted ones
        $bundle = $this->bundleRepository->findByUuidWithTrashed($bundle);

        if (!$bundle) {
            abort(404);
        }

        if (!$bundle->trashed()) {
            return redirect()->route('admin.bundles.trash')
                ->with('error', 'Bundle is not deleted!');
        }

        $this->bundleRepository->restore($bundle);

        return redirect()->route('admin.bundles.trash')
            ->with('success', 'Bundle restored successfully!');
    }

    // Force delete (permanently delete) bundle
    public function forceDelete($bundle): RedirectResponse
    {
        // Find bundle including soft deleted ones using UUID
        $bundleModel = $this->bundleRepository->findByUuidWithTrashed($bundle);

        if (!$bundleModel) {
            abort(404);
        }

        if (!$bundleModel->trashed()) {
            return redirect()->route('admin.bundles.trash')
                ->with('error', 'Bundle is not deleted!');
        }

        // Check if bundle is in any orders
        // Note: You may need to add a bundles relationship to orders if bundles can be in orders
        // For now, we'll just delete the image and bundle

        // Delete all bundle images only on permanent delete
        $images = $bundleModel->images;
        foreach ($images as $image) {
            $this->imageService->deleteImage($image->image);
            $image->delete();
        }

        // Detach all products from bundle before permanent delete
        $bundleModel->products()->detach();

        // Force delete the bundle
        $this->bundleRepository->forceDelete($bundleModel);

        return redirect()->route('admin.bundles.trash')
            ->with('success', 'Bundle permanently deleted!');
    }

    public function updateStatus(Request $request, $bundle)
    {
        // Find bundle using UUID
        $bundle = $this->bundleRepository->findByUuid($bundle);

        if (!$bundle) {
            return response()->json(['success' => false, 'message' => 'Bundle not found'], 404);
        }

        $request->validate(['status' => 'required|in:1,0']);
        $this->bundleRepository->updateStatus($bundle, $request->status);

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bundle status updated successfully!',
                'status' => $request->status
            ]);
        }

        return redirect()->back()->with('success', 'Bundle status updated successfully!');
    }
}
