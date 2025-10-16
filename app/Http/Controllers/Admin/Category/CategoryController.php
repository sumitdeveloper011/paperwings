<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Services\EposNowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {

        // $categories = $this->eposNow->getCategories();

        // foreach ($categories as $cat) {

        //     $arr = [
        //         'uuid' => Str::uuid(),
        //         'category_id' => $cat['Id'],
        //         'name' => $cat['Name'],
        //         'slug' => Str::slug($cat['Name']),
        //         'status' => 1,
        //         'image' => $cat['ImageUrl'] ? $cat['ImageUrl'] : null,
        //     ];
        //     $existing = Category::where('category_id', $cat['Id'])->first();

        //     if (! $existing) {
        //         $this->categoryRepository->create($arr);
        //     }
        //     // $this->categoryRepository->create($arr);

        // }
        // dd($arr);

        // dd('sdf');

        // foreach ($categories as $cat) {
        //     $imageData = $this->eposNow->uploadCategoryImage($cat['Id'], 'jpg', 'string');
        //     $categoryId = $cat['Id'];
        //     dump($cat);
        //     dump($imageData['CategoryImages'][0]['PreSignedUrl']);

        //     $imageData = file_get_contents($imageData['CategoryImages'][0]['PreSignedUrl']);
        //     if ($imageData === false) {
        //         dd('Failed to download image. URL may be expired: '.$imageData['CategoryImages'][0]['PreSignedUrl']);
        //     }
        //     dump('sdf');
        //     // $imageData = file_get_contents($imageData['CategoryImages'][0]['PreSignedUrl']);
        //     file_put_contents(public_path("images/categories/{$categoryId}.jpg"), $imageData);
        // }
        // dd('$categories');

        $search = $request->get('search');

        if ($search) {
            $categories = $this->categoryRepository->search($search);
            $categories = new \Illuminate\Pagination\LengthAwarePaginator(
                $categories,
                $categories->count(),
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $categories = $this->categoryRepository->paginate(10);
        }

        return view('admin.category.index', compact('categories', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'status' => 'required|in:1,0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
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

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        return view('admin.category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,'.$category->id,
            'status' => 'required|in:1,0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Delete image if exists
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $this->categoryRepository->delete($category);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Update category status
     */
    public function updateStatus(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:1,0',
        ]);

        $this->categoryRepository->updateStatus($category, $validated['status']);

        return redirect()->back()
            ->with('success', 'Category status updated successfully!');
    }
}
