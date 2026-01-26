<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductStatusRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAccordion;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Jobs\ImportEposNowProductsJob;
use App\Jobs\ImportEposNowStockJob;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;
use App\Services\EposNowService;
use App\Services\ImageService;
use App\Helpers\MetaHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $productRepository;

    protected CategoryRepositoryInterface $categoryRepository;

    protected SubCategoryRepositoryInterface $subCategoryRepository;

    protected BrandRepositoryInterface $brandRepository;

    protected $eposNow;
    protected ImageService $imageService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        SubCategoryRepositoryInterface $subCategoryRepository,
        BrandRepositoryInterface $brandRepository,
        EposNowService $eposNow,
        ImageService $imageService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->brandRepository = $brandRepository;
        $this->eposNow = $eposNow;
        $this->imageService = $imageService;
    }

    // Dispatch job to import products from EposNow
    public function getProductsForEposNow(Request $request): JsonResponse|RedirectResponse
    {
        try {
            // Pre-check API limit before starting import
            $apiCheck = $this->eposNow->checkApiLimit();

            if (!$apiCheck['available']) {
                $message = $apiCheck['message'] . ' Please wait 15-30 minutes and try again.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'status' => 'rate_limited',
                        'can_retry_after' => now()->addMinutes(30)->toDateTimeString()
                    ], 429);
                }

                return redirect()->route('admin.products.index')
                    ->with('error', $message);
            }

            // Continue with import if API is available
            $jobId = time() . '_' . uniqid();

            Cache::put("product_import_{$jobId}", [
                'percentage' => 0,
                'processed' => 0,
                'total' => 0,
                'message' => 'Job queued, waiting to start...',
                'status' => 'queued',
                'updated_at' => now()->toDateTimeString()
            ], 3600);

            // Always use async dispatch - don't use sync for long-running jobs
            ImportEposNowProductsJob::dispatch($jobId);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product import started successfully!',
                    'job_id' => $jobId
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product import started! Job ID: ' . $jobId)
                ->with('job_id', $jobId);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to start import: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.products.index')
                ->with('error', 'Failed to start import: ' . $e->getMessage());
        }
    }

    // Retry failed products from a previous import job
    public function retryFailedProducts(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $jobId = $request->input('jobId');

            if (!$jobId) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Job ID is required'
                    ], 400);
                }
                return redirect()->route('admin.products.index')
                    ->with('error', 'Job ID is required');
            }

            // Get failed items from cache
            $cacheKey = "product_import_{$jobId}";
            $progressData = Cache::get($cacheKey);

            if (!$progressData || empty($progressData['failed_items'])) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No failed products found for this job ID or job data has expired.'
                    ], 404);
                }
                return redirect()->route('admin.products.index')
                    ->with('error', 'No failed products found for this job ID or job data has expired.');
            }

            $failedItems = $progressData['failed_items'];
            $failedProductIds = array_map(function($item) {
                return $item['id'] ?? null;
            }, $failedItems);

            // Filter out invalid IDs
            $failedProductIds = array_filter($failedProductIds, function($id) {
                return $id !== null && $id !== 'unknown' && is_numeric($id);
            });

            if (empty($failedProductIds)) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid product IDs found in failed items.'
                    ], 400);
                }
                return redirect()->route('admin.products.index')
                    ->with('error', 'No valid product IDs found in failed items.');
            }

            // Pre-check API limit
            $apiCheck = $this->eposNow->checkApiLimit();

            if (!$apiCheck['available']) {
                $message = $apiCheck['message'] . ' Please wait 15-30 minutes and try again.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'status' => 'rate_limited'
                    ], 429);
                }

                return redirect()->route('admin.products.index')
                    ->with('error', $message);
            }

            // Create new job ID for retry
            $retryJobId = time() . '_retry_' . uniqid();

            Cache::put("product_import_{$retryJobId}", [
                'percentage' => 0,
                'processed' => 0,
                'total' => count($failedProductIds),
                'message' => 'Retrying failed products...',
                'status' => 'queued',
                'updated_at' => now()->toDateTimeString(),
                'failed_product_ids' => array_values($failedProductIds)
            ], 3600);

            // Dispatch job with specific product IDs
            ImportEposNowProductsJob::dispatch($retryJobId, array_values($failedProductIds));

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Retry started for ' . count($failedProductIds) . ' failed products!',
                    'job_id' => $retryJobId,
                    'failed_count' => count($failedProductIds)
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Retry started for ' . count($failedProductIds) . ' failed products! Job ID: ' . $retryJobId)
                ->with('job_id', $retryJobId);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.products.index')
                ->with('error', 'Failed to retry: ' . $e->getMessage());
        }
    }

    // Check import job status
    public function checkImportStatus(Request $request): JsonResponse
    {
        try {
            if (ob_get_level()) {
                ob_clean();
            }

            $jobId = $request->input('jobId');

            if (!$jobId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job ID is required',
                    'status' => 'missing_job_id'
                ], 400, [
                    'Content-Type' => 'application/json',
                ]);
            }

            $jobId = urldecode($jobId);

            $cacheKey = "product_import_{$jobId}";
            $progressData = Cache::get($cacheKey);

            if (!$progressData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found or expired. The import may still be processing, please wait...',
                    'status' => 'not_found',
                    'jobId' => $jobId,
                    'cache_key' => $cacheKey
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $progressData
            ], 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking status: ' . $e->getMessage()
            ], 500, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        }
    }

    public function getStockForEposNow(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $apiCheck = $this->eposNow->checkApiLimit();

            if (!$apiCheck['available']) {
                $message = $apiCheck['message'] . ' Please wait 15-30 minutes and try again.';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'status' => 'rate_limited',
                        'can_retry_after' => now()->addMinutes(30)->toDateTimeString()
                    ], 429);
                }

                return redirect()->route('admin.products.index')
                    ->with('error', $message);
            }

            $jobId = time() . '_' . uniqid();

            Cache::put("stock_import_{$jobId}", [
                'percentage' => 0,
                'processed' => 0,
                'total' => 0,
                'message' => 'Job queued, waiting to start...',
                'status' => 'queued',
                'updated_at' => now()->toDateTimeString()
            ], 3600);

            ImportEposNowStockJob::dispatch($jobId);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock import started successfully!',
                    'job_id' => $jobId
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Stock import started! Job ID: ' . $jobId)
                ->with('job_id', $jobId);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to start stock import: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.products.index')
                ->with('error', 'Failed to start stock import: ' . $e->getMessage());
        }
    }

    public function checkStockImportStatus(Request $request): JsonResponse
    {
        try {
            if (ob_get_level()) {
                ob_clean();
            }

            $jobId = $request->input('jobId');

            if (!$jobId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job ID is required',
                    'status' => 'missing_job_id'
                ], 400, [
                    'Content-Type' => 'application/json',
                ]);
            }

            $jobId = urldecode($jobId);

            $cacheKey = "stock_import_{$jobId}";
            $progressData = Cache::get($cacheKey);

            if (!$progressData) {
                $queuePosition = $this->getQueuePosition('imports', $jobId);
                
                if ($queuePosition > 0) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'percentage' => 0,
                            'processed' => 0,
                            'total' => 0,
                            'message' => "Job queued, waiting in queue (position: {$queuePosition}). Queue worker will process it shortly...",
                            'status' => 'queued',
                            'queue_position' => $queuePosition,
                            'updated_at' => now()->toDateTimeString()
                        ]
                    ], 200, [
                        'Content-Type' => 'application/json',
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0'
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found or expired. The import may still be processing, please wait...',
                    'status' => 'not_found',
                    'jobId' => $jobId,
                    'cache_key' => $cacheKey
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }

            $progressData['queue_position'] = $this->getQueuePosition('imports', $jobId);
            
            if ($progressData['status'] === 'queued' && $progressData['queue_position'] > 0) {
                $progressData['message'] = "Job queued, waiting in queue (position: {$progressData['queue_position']}). Queue worker will process it shortly...";
            }

            return response()->json([
                'success' => true,
                'data' => $progressData
            ], 200, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking status: ' . $e->getMessage()
            ], 500, [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        }
    }

    // Display a listing of the resource
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $categoryUuid = $request->get('category_id'); // Now accepts UUID

        // Build query - start fresh to avoid any default ordering
        // Exclude bundles (product_type = 4) as they are managed separately
        $query = Product::products()
            ->with(['category', 'images']);

        // Apply search filter
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%");
            });
        }

        // Apply category filter by UUID
        if ($categoryUuid && $categoryUuid !== '') {
            $category = Category::where('uuid', $categoryUuid)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Get paginated results with query parameters preserved
        // IMPORTANT: Order by created_at DESC (latest first), then by id DESC as secondary sort
        // This ensures newest products appear at the top of the list
        $products = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withPath($request->url())
            ->appends($request->except('page'));

        // Get categories for filter dropdown
        $categories = $this->categoryRepository->getActive();

        // Handle AJAX requests
        if ($this->isAjaxRequest($request) && $products instanceof LengthAwarePaginator) {
            $html = view('admin.product.partials.table', compact('products', 'categories'))->render();
            $paginationHtml = $products->hasPages() 
                ? '<div class="pagination-wrapper">' . view('components.pagination', ['paginator' => $products])->render() . '</div>'
                : '';

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $paginationHtml,
                'total' => $products->total(),
            ]);
        }

        return view('admin.product.index', compact(
            'products', 'search', 'categories', 'categoryUuid'
        ));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        $categories = $this->categoryRepository->getActive();
        $brands = $this->brandRepository->all();
        $tags = Tag::orderBy('name')->get();

        return view('admin.product.create', compact('categories', 'brands', 'tags'));
    }

    // Store a newly created resource in storage
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        unset($validated['images']);
        $accordionData = $validated['accordion_data'] ?? null;
        unset($validated['accordion_data']);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Generate UUID for product if not provided
        if (empty($validated['uuid'])) {
            $validated['uuid'] = Str::uuid()->toString();
        }
        $productUuid = $validated['uuid'];

        // Handle unified discount structure
        $discountType = $request->input('discount_type', 'none');
        $validated['discount_type'] = $discountType;

        if ($discountType === 'percentage' && $request->has('discount_percentage')) {
            $percentage = (float) $request->input('discount_percentage', 0);
            if ($percentage > 0 && isset($validated['total_price'])) {
                $validated['discount_value'] = $percentage;
                $discountAmount = $validated['total_price'] * ($percentage / 100);
                $validated['discount_price'] = round($validated['total_price'] - $discountAmount, 2);
            } else {
                $validated['discount_value'] = null;
                $validated['discount_price'] = null;
            }
        } elseif ($discountType === 'direct') {
            // Direct price - discount_value not used
            $validated['discount_value'] = null;
            // discount_price is already in validated array
        } else {
            // No discount
            $validated['discount_value'] = null;
            $validated['discount_price'] = null;
        }

        // Auto-fill meta fields if empty
        $validated = $this->autoFillMetaFields($validated);

        $product = $this->productRepository->create($validated);

        // Upload images using ImageService
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->imageService->uploadImage($image, 'products', $productUuid);
                if ($imagePath) {
                    // Check if image already exists for this product to prevent duplicates
                    $existingImage = ProductImage::where('product_id', $product->id)
                        ->where('image', $imagePath)
                        ->first();

                    if (!$existingImage) {
                        ProductImage::create([
                            'uuid' => Str::uuid(),
                            'product_id' => $product->id,
                            'eposnow_product_id' => $product->eposnow_product_id ?? null,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }
        }

        if ($accordionData && is_array($accordionData)) {
            foreach ($accordionData as $item) {
                if (! empty($item['heading']) && ! empty($item['content'])) {
                    ProductAccordion::create([
                        'uuid' => Str::uuid(),
                        'product_id' => $product->id,
                        'eposnow_product_id' => $product->eposnow_product_id ?? null,
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ]);
                }
            }
        }

        // Sync tags
        if ($request->has('tag_ids')) {
            $product->tags()->sync($request->tag_ids);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'images', 'accordions']);
        return view('admin.product.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load(['category', 'brand', 'images', 'accordions', 'tags']);
        $categories = $this->categoryRepository->getActive();
        $brands = $this->brandRepository->all();
        $tags = Tag::orderBy('name')->get();

        return view('admin.product.edit', compact('product', 'categories', 'brands', 'tags'));
    }

    // Update the specified resource in storage
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        unset($validated['images']);
        $accordionData = $validated['accordion_data'] ?? null;
        unset($validated['accordion_data']);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle unified discount structure
        $discountType = $request->input('discount_type', 'none');
        $validated['discount_type'] = $discountType;

        if ($discountType === 'percentage' && $request->has('discount_percentage')) {
            $percentage = (float) $request->input('discount_percentage', 0);
            if ($percentage > 0 && isset($validated['total_price'])) {
                $validated['discount_value'] = $percentage;
                $discountAmount = $validated['total_price'] * ($percentage / 100);
                $validated['discount_price'] = round($validated['total_price'] - $discountAmount, 2);
            } else {
                $validated['discount_value'] = null;
                $validated['discount_price'] = null;
            }
        } elseif ($discountType === 'direct') {
            // Direct price - discount_value not used
            $validated['discount_value'] = null;
            // discount_price is already in validated array
        } else {
            // No discount
            $validated['discount_value'] = null;
            $validated['discount_price'] = null;
        }

        // Auto-fill meta fields if empty
        $validated = $this->autoFillMetaFields($validated, $product);

        $this->productRepository->update($product, $validated);

        // Delete old images if not keeping existing
        if (! $request->boolean('keep_existing_images')) {
            $oldImages = $product->images;
            foreach ($oldImages as $oldImage) {
                $this->imageService->deleteImage($oldImage->image);
                $oldImage->delete();
            }
        }

        // Upload new images using ImageService
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $this->imageService->uploadImage($image, 'products', $product->uuid);
                if ($imagePath) {
                    // Check if image already exists for this product to prevent duplicates
                    $existingImage = ProductImage::where('product_id', $product->id)
                        ->where('image', $imagePath)
                        ->first();

                    if (!$existingImage) {
                        ProductImage::create([
                            'uuid' => Str::uuid(),
                            'product_id' => $product->id,
                            'eposnow_product_id' => $product->eposnow_product_id ?? null,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }
        }

        $product->accordions()->delete();

        if ($accordionData && is_array($accordionData)) {
            foreach ($accordionData as $item) {
                if (! empty($item['heading']) && ! empty($item['content'])) {
                    ProductAccordion::create([
                        'uuid' => Str::uuid(),
                        'product_id' => $product->id,
                        'eposnow_product_id' => $product->eposnow_product_id ?? null,
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ]);
                }
            }
        }

        // Sync tags
        if ($request->has('tag_ids')) {
            $product->tags()->sync($request->tag_ids);
        } else {
            $product->tags()->sync([]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    // Remove the specified resource from storage (Soft Delete)
    public function destroy(Product $product): RedirectResponse
    {
        // Soft delete the product (images and accordions remain intact for restore)
        // Images and accordions will only be deleted on forceDelete (permanent delete)
        $this->productRepository->delete($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    // Show trashed (soft deleted) products
    public function trash(Request $request): View|JsonResponse
    {
        $search = trim($request->get('search', ''));

        // Build query for trashed products
        // Exclude bundles (product_type = 4) as they are managed separately
        $query = Product::onlyTrashed()
            ->products()
            ->with(['category', 'images']);

        // Apply search filter
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('short_description', 'LIKE', "%{$search}%");
            });
        }

        // Get paginated results
        $products = $query->orderBy('deleted_at', 'desc')
            ->paginate(15)
            ->withPath($request->url())
            ->appends($request->except('page'));

        // Get categories for filter dropdown
        $categories = $this->categoryRepository->getActive();

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($products instanceof LengthAwarePaginator) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $products
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.product.partials.table', compact('products'))->render(),
                'pagination' => $paginationHtml,
            ]);
        }

        return view('admin.product.trash', compact('products', 'search', 'categories'));
    }

    // Restore soft deleted product
    public function restore($product): RedirectResponse
    {
        // Find product including soft deleted ones
        $product = Product::withTrashed()->where('uuid', $product)->firstOrFail();

        if (!$product->trashed()) {
            return redirect()->route('admin.products.trash')
                ->with('error', 'Product is not deleted!');
        }

        $this->productRepository->restore($product);

        return redirect()->route('admin.products.trash')
            ->with('success', 'Product restored successfully!');
    }

    public function forceDelete($product): RedirectResponse
    {
        // Find product including soft deleted ones using UUID
        $productModel = Product::withTrashed()->where('uuid', $product)->firstOrFail();

        if (!$productModel->trashed()) {
            return redirect()->route('admin.products.trash')
                ->with('error', 'Product is not deleted!');
        }

        // Check if product is in any orders
        $orderItemsCount = OrderItem::where('product_id', $productModel->id)->count();

        if ($orderItemsCount > 0) {
            return redirect()->route('admin.products.trash')
                ->with('error', "Cannot permanently delete this product! It is associated with {$orderItemsCount} order(s). Products in orders must be kept for order history.");
        }

        // Delete all product images using ImageService
        $images = $productModel->images;
        foreach ($images as $image) {
            $this->imageService->deleteImage($image->image);
            $image->delete();
        }

        $productModel->accordions()->delete();

        $this->productRepository->forceDelete($productModel);

        return redirect()->route('admin.products.trash')
            ->with('success', 'Product permanently deleted!');
    }

    // Update product status
    public function updateStatus(UpdateProductStatusRequest $request, Product $product)
    {
        $validated = $request->validated();

        $this->productRepository->updateStatus($product, $validated['status']);

        $statusLabel = $validated['status'] == 1 ? 'Active' : 'Inactive';

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Product set to {$statusLabel}",
                'status' => $validated['status']
            ]);
        }

        return redirect()->back()
            ->with('success', "Product set to {$statusLabel}");
    }

    // Get subcategories by category (AJAX)
    public function getSubCategories(Request $request)
    {
        $categoryId = $request->get('category_id');
        $subCategories = $this->subCategoryRepository->getActiveByCategory($categoryId);

        return response()->json($subCategories->map(function ($subCategory) {
            return [
                'id' => $subCategory->id,
                'name' => $subCategory->name,
            ];
        }));
    }

    // Import product images from EposNow
    /**
     * Auto-fill meta fields if they are empty
     */
    protected function autoFillMetaFields(array $validated, ?Product $product = null): array
    {
        return MetaHelper::autoFillMetaFields($validated, $product);
    }

    /**
     * Get queue position for a job
     *
     * @param string $queue
     * @param string $jobId
     * @return int Queue position (0 if not found or processing)
     */
    protected function getQueuePosition(string $queue, string $jobId): int
    {
        try {
            $pendingJobs = DB::table('jobs')
                ->where('queue', $queue)
                ->count();
            
            if ($pendingJobs > 0) {
                return $pendingJobs;
            }
            
            return 0;
        } catch (\Exception $e) {
            Log::warning('Failed to get queue position', [
                'queue' => $queue,
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

}


