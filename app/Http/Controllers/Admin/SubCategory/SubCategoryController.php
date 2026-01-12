<?php

namespace App\Http\Controllers\Admin\SubCategory;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ImageService;

class SubCategoryController extends Controller
{
    protected SubCategoryRepositoryInterface $subCategoryRepository;
    protected CategoryRepositoryInterface $categoryRepository;
    protected ImageService $imageService;

    public function __construct(
        SubCategoryRepositoryInterface $subCategoryRepository,
        CategoryRepositoryInterface $categoryRepository,
        ImageService $imageService
    ) {
        $this->subCategoryRepository = $subCategoryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageService = $imageService;
    }

    // Display a listing of the resource
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        if ($search) {
            $subCategories = $this->subCategoryRepository->search($search);
            $subCategories = new LengthAwarePaginator(
                $subCategories,
                $subCategories->count(),
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } elseif ($categoryId) {
            $subCategories = $this->subCategoryRepository->getByCategory($categoryId);
            $subCategories = new LengthAwarePaginator(
                $subCategories,
                $subCategories->count(),
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $subCategories = $this->subCategoryRepository->paginate(10);
        }

        $categories = $this->categoryRepository->getActive();

        return view('admin.subcategory.index', compact('subCategories', 'search', 'categories', 'categoryId'));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        $categories = $this->categoryRepository->getActive();
        return view('admin.subcategory.create', compact('categories'));
    }

    // Store a newly created resource in storage
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:subcategories,name',
            'slug' => 'nullable|string|max:255|unique:subcategories,slug',
            'status' => 'required|in:1,0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->uploadImage(
                $request->file('image'),
                'subcategories',
                null,
                null,
                false
            );
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->subCategoryRepository->create($validated);

        return redirect()->route('admin.subcategories.index')
                        ->with('success', 'SubCategory created successfully!');
    }

    // Display the specified resource
    public function show(SubCategory $subcategory): View
    {
        $subcategory->load('category');
        return view('admin.subcategory.show', compact('subcategory'));
    }

    // Show the form for editing the specified resource
    public function edit(SubCategory $subcategory): View
    {
        $subcategory->load('category');
        $categories = $this->categoryRepository->getActive();
        return view('admin.subcategory.edit', compact('subcategory', 'categories'));
    }

    // Update the specified resource in storage
    public function update(Request $request, SubCategory $subcategory): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:subcategories,name,' . $subcategory->id,
            'slug' => 'nullable|string|max:255|unique:subcategories,slug,' . $subcategory->id,
            'status' => 'required|in:1,0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->uploadSimple(
                $request->file('image'),
                'subcategories',
                $subcategory->image
            );
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->subCategoryRepository->update($subcategory, $validated);

        return redirect()->route('admin.subcategories.index')
                        ->with('success', 'SubCategory updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(SubCategory $subcategory): RedirectResponse
    {
        if ($subcategory->image) {
            $this->imageService->deleteImage($subcategory->image);
        }

        $this->subCategoryRepository->delete($subcategory);

        return redirect()->route('admin.subcategories.index')
                        ->with('success', 'SubCategory deleted successfully!');
    }

    // Update subcategory status
    public function updateStatus(Request $request, SubCategory $subcategory): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:1,0'
        ]);

        $this->subCategoryRepository->updateStatus($subcategory, $validated['status']);

        return redirect()->back()
                        ->with('success', 'SubCategory status updated successfully!');
    }
}
