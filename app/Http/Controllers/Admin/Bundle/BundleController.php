<?php

namespace App\Http\Controllers\Admin\Bundle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bundle\StoreBundleRequest;
use App\Http\Requests\Admin\Bundle\UpdateBundleRequest;
use App\Models\Product;
use App\Models\ProductAccordion;
use App\Models\ProductImage;
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
    protected ImageService $imageService;

    public function __construct(
        ImageService $imageService
    ) {
        $this->imageService = $imageService;
    }

    /**
     * Get Bundles category
     */
    private function getBundlesCategory()
    {
        return \App\Models\Category::where('slug', 'bundles')->firstOrFail();
    }
    public function index(Request $request): View|JsonResponse
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status');

        // Query products with product_type = 4 (bundles)
        $query = Product::bundles()
            ->withCount('bundleProducts')
            ->with('images');

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
        $bundles = $query->orderBy('sort_order')
            ->orderBy('name')
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
        $bundlesCategory = $this->getBundlesCategory();
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

        return view('admin.bundle.create', compact('bundlesCategory', 'categories', 'oldProducts', 'oldProductIds', 'oldQuantities'));
    }

    public function store(StoreBundleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $bundlesCategory = $this->getBundlesCategory();

        // Generate UUID and slug
        $bundleUuid = Str::uuid()->toString();
        $slug = Str::slug($validated['name']);

        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle unified discount structure
        $discountType = $request->input('discount_type', 'none');
        $discountValue = null;
        $discountPrice = null;

        if ($discountType === 'percentage' && $request->has('discount_percentage')) {
            $percentage = (float) $request->input('discount_percentage', 0);
            if ($percentage > 0 && $percentage <= 100) {
                $discountValue = $percentage;
                $bundlePrice = $validated['bundle_price'];
                $discountAmount = $bundlePrice * ($percentage / 100);
                $discountPrice = round(max(0, $bundlePrice - $discountAmount), 2);
            }
        } elseif ($discountType === 'direct' && $request->has('discount_price')) {
            $discountPrice = (float) $request->input('discount_price', 0);
            if ($discountPrice > 0 && $discountPrice < $validated['bundle_price']) {
                // discount_price is the final price customer pays
            } else {
                $discountPrice = null;
                $discountType = 'none';
            }
        } else {
            $discountType = 'none';
        }

        // Create product as bundle
        $product = Product::create([
            'uuid' => $bundleUuid,
            'category_id' => $bundlesCategory->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'],
            'total_price' => $validated['bundle_price'], // Map bundle_price to total_price
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_price' => $discountPrice,
            'product_type' => 4, // Bundle type
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'] ? 1 : 0,
            'meta_title' => $validated['name'],
            'meta_description' => $validated['short_description'],
        ]);

        // Upload images using ImageService (to ProductImage)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->imageService->uploadImage($image, 'products', $bundleUuid);
                if ($imagePath) {
                    $existingImage = ProductImage::where('product_id', $product->id)
                        ->where('image', $imagePath)
                        ->first();

                    if (!$existingImage) {
                        ProductImage::create([
                            'uuid' => Str::uuid(),
                            'product_id' => $product->id,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }
        }

        // Attach products via product_bundle_items
        $productsData = [];
        $productIds = array_unique($validated['product_ids']);

        foreach ($productIds as $productId) {
            $originalIndex = array_search($productId, $validated['product_ids']);
            $quantity = isset($validated['quantities'][$originalIndex])
                ? $validated['quantities'][$originalIndex]
                : 1;

            $productsData[$productId] = [
                'quantity' => max(1, (int)$quantity)
            ];
        }

        // Use sync to attach products
        $product->bundleProducts()->sync($productsData);

        // Handle accordion data (create as ProductAccordion)
        if ($request->has('accordion_data') && is_array($request->accordion_data)) {
            foreach ($request->accordion_data as $item) {
                if (!empty($item['heading']) && !empty($item['content'])) {
                    ProductAccordion::create([
                        'uuid' => Str::uuid(),
                        'product_id' => $product->id,
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle created successfully!');
    }

    public function show($bundle): View
    {
        // Find bundle (product with product_type = 4)
        $bundle = Product::bundles()
            ->where('uuid', $bundle)
            ->with(['bundleProducts.images', 'images', 'accordions'])
            ->firstOrFail();

        return view('admin.bundle.show', compact('bundle'));
    }

    public function edit($bundle): View|RedirectResponse
    {
        // Find bundle (product with product_type = 4)
        $bundle = Product::bundles()
            ->where('uuid', $bundle)
            ->firstOrFail();

        // Don't allow editing trashed bundles
        if ($bundle->trashed()) {
            return redirect()->route('admin.bundles.trash')
                ->with('error', 'Cannot edit a deleted bundle. Please restore it first.');
        }

        $bundlesCategory = $this->getBundlesCategory();
        $bundle->load(['bundleProducts', 'images', 'accordions']);
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

        return view('admin.bundle.edit', compact('bundle', 'bundlesCategory', 'categories', 'oldProducts', 'oldProductIds', 'oldQuantities'));
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
        // Find bundle (product with product_type = 4)
        $bundle = Product::bundles()
            ->where('uuid', $bundle)
            ->firstOrFail();

        // Don't allow updating trashed bundles
        if ($bundle->trashed()) {
            return redirect()->route('admin.bundles.trash')
                ->with('error', 'Cannot update a deleted bundle. Please restore it first.');
        }

        $validated = $request->validated();

        // Handle unified discount structure
        $discountType = $request->input('discount_type', 'none');
        $discountValue = null;
        $discountPrice = null;

        if ($discountType === 'percentage' && $request->has('discount_percentage')) {
            $percentage = (float) $request->input('discount_percentage', 0);
            if ($percentage > 0 && $percentage <= 100) {
                $discountValue = $percentage;
                $bundlePrice = $validated['bundle_price'];
                $discountAmount = $bundlePrice * ($percentage / 100);
                $discountPrice = round(max(0, $bundlePrice - $discountAmount), 2);
            }
        } elseif ($discountType === 'direct' && $request->has('discount_price')) {
            $discountPrice = (float) $request->input('discount_price', 0);
            if ($discountPrice > 0 && $discountPrice < $validated['bundle_price']) {
                // discount_price is the final price customer pays
            } else {
                $discountPrice = null;
                $discountType = 'none';
            }
        } else {
            $discountType = 'none';
        }

        // Update product
        $bundle->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'short_description' => $validated['short_description'],
            'total_price' => $validated['bundle_price'], // Map bundle_price to total_price
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_price' => $discountPrice,
            'status' => $validated['status'] ? 1 : 0,
            'sort_order' => $validated['sort_order'] ?? $bundle->sort_order,
        ]);

        // Handle images (same as store, but with keep_existing_images check)
        if (!$request->boolean('keep_existing_images')) {
            $oldImages = $bundle->images;
            foreach ($oldImages as $oldImage) {
                $this->imageService->deleteImage($oldImage->image);
                $oldImage->delete();
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->imageService->uploadImage($image, 'products', $bundle->uuid);
                if ($imagePath) {
                    $existingImage = ProductImage::where('product_id', $bundle->id)
                        ->where('image', $imagePath)
                        ->first();

                    if (!$existingImage) {
                        ProductImage::create([
                            'uuid' => Str::uuid(),
                            'product_id' => $bundle->id,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }
        }

        // Sync products
        $productsData = [];
        $productIds = array_unique($validated['product_ids']);

        foreach ($productIds as $productId) {
            $originalIndex = array_search($productId, $validated['product_ids']);
            $quantity = isset($validated['quantities'][$originalIndex])
                ? $validated['quantities'][$originalIndex]
                : 1;

            $productsData[$productId] = [
                'quantity' => max(1, (int)$quantity)
            ];
        }

        $bundle->bundleProducts()->sync($productsData);

        // Handle accordion data
        $bundle->accordions()->delete();

        if ($request->has('accordion_data') && is_array($request->accordion_data)) {
            foreach ($request->accordion_data as $item) {
                if (!empty($item['heading']) && !empty($item['content'])) {
                    ProductAccordion::create([
                        'uuid' => Str::uuid(),
                        'product_id' => $bundle->id,
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ]);
                }
            }
        }

        $bundle->refresh();

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle updated successfully!');
    }

    // Remove the specified resource from storage (Soft Delete)
    public function destroy($bundle): RedirectResponse
    {
        // Find bundle using UUID
        $bundle = Product::bundles()
            ->where('uuid', $bundle)
            ->firstOrFail();

        // Soft delete the bundle
        $bundle->delete();

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle deleted successfully!');
    }

    // Show trashed (soft deleted) bundles
    public function trash(Request $request): View
    {
        $search = trim($request->get('search', ''));

        // Build query for trashed bundles
        $query = Product::bundles()
            ->onlyTrashed()
            ->withCount('bundleProducts')
            ->with('images');

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
        $bundle = Product::bundles()
            ->withTrashed()
            ->where('uuid', $bundle)
            ->firstOrFail();

        if (!$bundle->trashed()) {
            return redirect()->route('admin.bundles.trash')
                ->with('error', 'Bundle is not deleted!');
        }

        $bundle->restore();

        return redirect()->route('admin.bundles.trash')
            ->with('success', 'Bundle restored successfully!');
    }

    // Force delete (permanently delete) bundle
    public function forceDelete($bundle): RedirectResponse
    {
        // Find bundle including soft deleted ones using UUID
        $bundleModel = Product::bundles()
            ->withTrashed()
            ->where('uuid', $bundle)
            ->firstOrFail();

        if (!$bundleModel->trashed()) {
            return redirect()->route('admin.bundles.trash')
                ->with('error', 'Bundle is not deleted!');
        }

        // Delete all bundle images only on permanent delete
        $images = $bundleModel->images;
        foreach ($images as $image) {
            $this->imageService->deleteImage($image->image);
            $image->delete();
        }

        // Detach all products from bundle before permanent delete
        $bundleModel->bundleProducts()->detach();

        // Force delete the bundle
        $bundleModel->forceDelete();

        return redirect()->route('admin.bundles.trash')
            ->with('success', 'Bundle permanently deleted!');
    }

    public function updateStatus(Request $request, $bundle)
    {
        // Find bundle using UUID
        $bundle = Product::bundles()
            ->where('uuid', $bundle)
            ->firstOrFail();

        $request->validate(['status' => 'required|in:1,0']);
        $bundle->update(['status' => $request->status]);

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
