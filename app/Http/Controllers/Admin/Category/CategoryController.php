<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryStatusRequest;
use App\Jobs\ImportEposNowCategoriesJob;
use App\Models\Category;
use App\Models\Product;
use App\Models\Product;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Services\EposNowService;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    protected CategoryRepositoryInterface $categoryRepository;
    protected $eposNow;
    protected ImageService $imageService;

    public function __construct(CategoryRepositoryInterface $categoryRepository, EposNowService $eposNow, ImageService $imageService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->eposNow = $eposNow;
        $this->imageService = $imageService;
    }

    // Dispatch job to import categories from EposNow
    public function getCategoriesForEposNow(Request $request): JsonResponse|RedirectResponse
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

                return redirect()->route('admin.categories.index')
                    ->with('error', $message);
            }

            // Continue with import if API is available
            $jobId = time() . '_' . uniqid();

            Cache::put("category_import_{$jobId}", [
                'percentage' => 0,
                'processed' => 0,
                'total' => 0,
                'message' => 'Job queued, waiting to start...',
                'status' => 'queued',
                'updated_at' => now()->toDateTimeString()
            ], 3600);

            if (config('queue.default') === 'sync') {
                ImportEposNowCategoriesJob::dispatchSync($jobId);
            } else {
                ImportEposNowCategoriesJob::dispatch($jobId);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Category import started successfully!',
                    'job_id' => $jobId
                ]);
            }

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category import started! Job ID: ' . $jobId)
                ->with('job_id', $jobId);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to start import: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.categories.index')
                ->with('error', 'Failed to start import: ' . $e->getMessage());
        }
    }

    // Check import job status
    public function checkImportStatus(Request $request): JsonResponse
    {
        \Log::info('checkImportStatus method called', [
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

            $cacheKey = "category_import_{$jobId}";
            \Log::info('Import status check', [
                'jobId' => $jobId,
                'cache_key' => $cacheKey,
                'request_url' => $request->fullUrl(),
            ]);

            $progressData = Cache::get($cacheKey);

            \Log::info('Cache check result', [
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
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        if ($search !== '') {
            $searchResults = $this->categoryRepository->search($search);
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 10;
            $total = $searchResults->count();
            $items = $searchResults->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $categories = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'query' => request()->query()
                ]
            );
        } else {
            $categories = $this->categoryRepository->paginate(10);
        }

        // Load product counts for each category
        $categoryIds = $categories->pluck('id')->toArray();
        $productCounts = Product::whereIn('category_id', $categoryIds)
            ->selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->pluck('count', 'category_id')
            ->toArray();

        // Add product count to each category
        foreach ($categories as $category) {
            $category->products_count = $productCounts[$category->id] ?? 0;
        }

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            // Check if categories is a paginator instance
            if ($categories instanceof LengthAwarePaginator && $categories->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $categories
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.category.partials.table', compact('categories'))->render(),
                'pagination' => $paginationHtml,
                'total' => $categories->total(),
            ]);
        }

        return view('admin.category.index', compact('categories', 'search'));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        return view('admin.category.create');
    }

    // Store a newly created resource in storage
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Generate UUID first (will be used for folder name)
        $categoryUuid = Str::uuid()->toString();
        $validated['uuid'] = $categoryUuid;

        // Upload image with category UUID if provided
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->uploadImage($request->file('image'), 'categories', $categoryUuid);
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->categoryRepository->create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    // Display the specified resource
    public function show(Category $category): View
    {
        return view('admin.category.show', compact('category'));
    }

    // Show the form for editing the specified resource
    public function edit(Category $category): View
    {
        return view('admin.category.edit', compact('category'));
    }

    // Update the specified resource in storage
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        // Use existing category UUID for folder name
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->updateImage($request->file('image'), 'categories', $category->uuid, $category->image);
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->categoryRepository->update($category, $validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    // Show products linked to a category
    public function products(Category $category, Request $request): View
    {
        $products = $category->products()->paginate(15);

        return view('admin.category.products', compact('category', 'products'));
    }

    // Remove the specified resource from storage
    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has products
        $productsCount = $category->products()->count();

        if ($productsCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Cannot delete category. This category has {$productsCount} product(s) associated with it. Please remove or reassign products before deleting the category.");
        }

        // Delete image and its folder
        $this->imageService->deleteImage($category->image);

        $this->categoryRepository->delete($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    // Update category status
    public function updateStatus(UpdateCategoryStatusRequest $request, Category $category)
    {
        $validated = $request->validated();

        $this->categoryRepository->updateStatus($category, $validated['status']);

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully!',
                'status' => $validated['status']
            ]);
        }

        return redirect()->back()
            ->with('success', 'Category status updated successfully!');
    }
}
