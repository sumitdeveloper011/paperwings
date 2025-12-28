<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductStatusRequest;
use App\Models\Product;
use App\Models\ProductAccordion;
use App\Models\ProductImage;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Jobs\ImportEposNowProductsJob;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;
use App\Services\EposNowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    protected ProductRepositoryInterface $productRepository;

    protected CategoryRepositoryInterface $categoryRepository;

    protected SubCategoryRepositoryInterface $subCategoryRepository;

    protected BrandRepositoryInterface $brandRepository;

    protected $eposNow;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        SubCategoryRepositoryInterface $subCategoryRepository,
        BrandRepositoryInterface $brandRepository,
        EposNowService $eposNow,
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->subCategoryRepository = $subCategoryRepository;
        $this->brandRepository = $brandRepository;
        $this->eposNow = $eposNow;
    }

    // Dispatch job to import products from EposNow
    public function getProductsForEposNow(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $jobId = time() . '_' . uniqid();

            Cache::put("product_import_{$jobId}", [
                'percentage' => 0,
                'processed' => 0,
                'total' => 0,
                'message' => 'Job queued, waiting to start...',
                'status' => 'queued',
                'updated_at' => now()->toDateTimeString()
            ], 3600);

            if (config('queue.default') === 'sync') {
                ImportEposNowProductsJob::dispatchSync($jobId);
            } else {
                ImportEposNowProductsJob::dispatch($jobId);
            }

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

    // Check import job status
    public function checkImportStatus(Request $request): JsonResponse
    {
        \Log::info('checkImportStatus method called (products)', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'all_input' => $request->all()
        ]);
        
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
            \Log::info('Product import status check', [
                'jobId' => $jobId,
                'cache_key' => $cacheKey,
                'request_url' => $request->fullUrl(),
            ]);
            
            $progressData = Cache::get($cacheKey);
            
            \Log::info('Product cache check result', [
                'cache_key' => $cacheKey,
                'has_data' => !is_null($progressData),
                'data' => $progressData
            ]);

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

    // Display a listing of the resource
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $brandId = $request->get('brand_id');

        if ($search) {
            $products = $this->productRepository->search($search);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $products->count(),
                10,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } elseif ($categoryId) {
            $products = $this->productRepository->getByCategory($categoryId);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $products->count(),
                10,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } elseif ($brandId) {
            $products = $this->productRepository->getByBrand($brandId);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $products->count(),
                10,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $products = $this->productRepository->paginate(10);
        }

        $categories = $this->categoryRepository->getActive();
        $brands = $this->brandRepository->all();
        return view('admin.product.index', compact(
            'products', 'search', 'categories', 'brands',
            'categoryId', 'brandId'
        ));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        $categories = $this->categoryRepository->getActive();
        $brands = $this->brandRepository->all();

        return view('admin.product.create', compact('categories', 'brands'));
    }

    // Store a newly created resource in storage
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $imagePaths[] = $imagePath;
            }
        }

        unset($validated['images']);
        $accordionData = $validated['accordion_data'] ?? null;
        unset($validated['accordion_data']);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product = $this->productRepository->create($validated);

        if (! empty($imagePaths)) {
            foreach ($imagePaths as $imagePath) {
                ProductImage::create([
                    'uuid' => Str::uuid(),
                    'product_id' => $product->id,
                    'eposnow_product_id' => $product->eposnow_product_id ?? $product->id,
                    'image' => $imagePath,
                ]);
            }
        }

        if ($accordionData && is_array($accordionData)) {
            foreach ($accordionData as $item) {
                if (! empty($item['heading']) && ! empty($item['content'])) {
                    ProductAccordion::create([
                        'uuid' => Str::uuid(),
                        'product_id' => $product->id,
                        'eposnow_product_id' => $product->eposnow_product_id ?? $product->id,
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    // Display the specified resource
    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'images', 'accordions']);
        return view('admin.product.show', compact('product'));
    }

    // Show the form for editing the specified resource
    public function edit(Product $product): View
    {
        $product->load(['category', 'brand', 'images', 'accordions']);
        $categories = $this->categoryRepository->getActive();
        $brands = $this->brandRepository->all();

        return view('admin.product.edit', compact('product', 'categories', 'brands'));
    }

    // Update the specified resource in storage
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $newImagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $newImagePaths[] = $imagePath;
            }
        }

        unset($validated['images']);
        $accordionData = $validated['accordion_data'] ?? null;
        unset($validated['accordion_data']);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->productRepository->update($product, $validated);

        if (! $request->boolean('keep_existing_images')) {
            $oldImages = $product->images;
            foreach ($oldImages as $oldImage) {
                if (Storage::disk('public')->exists($oldImage->image)) {
                    Storage::disk('public')->delete($oldImage->image);
                }
                $oldImage->delete();
            }
        }

        if (! empty($newImagePaths)) {
            foreach ($newImagePaths as $imagePath) {
                ProductImage::create([
                    'uuid' => Str::uuid(),
                    'product_id' => $product->id,
                    'eposnow_product_id' => $product->eposnow_product_id ?? $product->id,
                    'image' => $imagePath,
                ]);
            }
        }

        $product->accordions()->delete();

        if ($accordionData && is_array($accordionData)) {
            foreach ($accordionData as $item) {
                if (! empty($item['heading']) && ! empty($item['content'])) {
                    ProductAccordion::create([
                        'uuid' => Str::uuid(),
                        'product_id' => $product->id,
                        'eposnow_product_id' => $product->eposnow_product_id ?? $product->id,
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(Product $product): RedirectResponse
    {
        $images = $product->images;
        foreach ($images as $image) {
            if (Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
            $image->delete();
        }

        $product->accordions()->delete();

        $this->productRepository->delete($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    // Update product status
    public function updateStatus(UpdateProductStatusRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $this->productRepository->updateStatus($product, $validated['status']);

        return redirect()->back()
            ->with('success', 'Product status updated successfully!');
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
    protected function importProductImages(string $eposnowProductId, int $productId): void
    {
        try {
            $images = $this->eposNow->getProductImages($eposnowProductId);

            if (empty($images)) {
                return;
            }

            foreach ($images as $imageData) {
                if (empty($imageData['ImageUrl']) || !is_string($imageData['ImageUrl'])) {
                    continue;
                }

                $imageUrl = $imageData['ImageUrl'];

                if ($imageUrl === 'string' || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    continue;
                }

                $isMainImage = $imageData['MainImage'] ?? false;

                $savedImagePath = $this->eposNow->downloadAndSaveImage($imageUrl, $eposnowProductId, $isMainImage);

                if ($savedImagePath) {
                    ProductImage::create([
                        'uuid' => Str::uuid(),
                        'product_id' => $productId,
                        'eposnow_product_id' => $eposnowProductId,
                        'image' => $savedImagePath,
                    ]);
                }

                usleep(100000);
            }
        } catch (\Throwable $e) {
        }
    }

    // Import images for all products from EposNow
    public function importAllProductImages(): RedirectResponse
    {
        try {
            $products = Product::whereNotNull('eposnow_product_id')
                ->whereDoesntHave('images')
                ->get();

            if ($products->isEmpty()) {
                return redirect()
                    ->route('admin.products.index')
                    ->with('info', 'All products already have images or no products found without images.');
            }

            $totalProcessed = 0;
            $totalImagesDownloaded = 0;
            $failed = 0;

            foreach ($products as $product) {
                try {
                    $beforeCount = $product->images()->count();
                    $this->importProductImages((string) $product->eposnow_product_id, $product->id);
                    $afterCount = $product->fresh()->images()->count();
                    $downloaded = $afterCount - $beforeCount;

                    if ($downloaded > 0) {
                        $totalImagesDownloaded += $downloaded;
                    }

                    $totalProcessed++;
                    usleep(200000);
                } catch (\Throwable $e) {
                    $failed++;
                    continue;
                }
            }

            $message = "Image import completed! Processed: {$totalProcessed} products, Downloaded: {$totalImagesDownloaded} images";
            if ($failed > 0) {
                $message .= ", Failed: {$failed}";
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', $message);

        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Failed to import product images: ' . $e->getMessage());
        }
    }
}


