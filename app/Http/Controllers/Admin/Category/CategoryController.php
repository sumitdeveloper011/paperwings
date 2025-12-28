<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryStatusRequest;
use App\Jobs\ImportEposNowCategoriesJob;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Services\EposNowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    protected CategoryRepositoryInterface $categoryRepository;

    protected $eposNow;

    public function __construct(CategoryRepositoryInterface $categoryRepository, EposNowService $eposNow)
    {
        $this->categoryRepository = $categoryRepository;
        $this->eposNow = $eposNow;
    }

    // Dispatch job to import categories from EposNow
    public function getCategoriesForEposNow(Request $request): JsonResponse|RedirectResponse
    {
        try {
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
    public function index(Request $request): View
    {
        $search = $request->get('search');

        if ($search) {
            $categories = $this->categoryRepository->search($search);
            $categories = new \Illuminate\Pagination\LengthAwarePaginator(
                $categories,
                $categories->count(),
                10,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $categories = $this->categoryRepository->paginate(10);
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

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories', $imageName, 'public');
            $validated['image'] = $imagePath;
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

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $image = $request->file('image');
            $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
            $imagePath = $image->storeAs('categories', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->categoryRepository->update($category, $validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $this->categoryRepository->delete($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    // Update category status
    public function updateStatus(UpdateCategoryStatusRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $this->categoryRepository->updateStatus($category, $validated['status']);

        return redirect()->back()
            ->with('success', 'Category status updated successfully!');
    }
}
