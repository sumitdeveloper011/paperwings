<?php

namespace App\Http\Controllers\Admin\Brand;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    protected BrandRepositoryInterface $brandRepository;
    protected ImageService $imageService;

    public function __construct(BrandRepositoryInterface $brandRepository, ImageService $imageService)
    {
        $this->brandRepository = $brandRepository;
        $this->imageService = $imageService;
    }

    // Display a listing of the resource
    public function index(Request $request): View
    {
        $search = $request->get('search');

        if ($search) {
            $brands = $this->brandRepository->search($search);
            $brands = new \Illuminate\Pagination\LengthAwarePaginator(
                $brands,
                $brands->count(),
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $brands = $this->brandRepository->paginate(10);
        }

        return view('admin.brand.index', compact('brands', 'search'));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        return view('admin.brand.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Generate UUID first (will be used for folder name)
        $brandUuid = Str::uuid()->toString();
        $validated['uuid'] = $brandUuid;

        // Upload image with brand UUID using ImageService
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->uploadImage($request->file('image'), 'brands', $brandUuid);
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->brandRepository->create($validated);

        return redirect()->route('admin.brands.index')
                        ->with('success', 'Brand created successfully!');
    }

    // Display the specified resource
    public function show(Brand $brand): View
    {
        return view('admin.brand.show', compact('brand'));
    }

    // Show the form for editing the specified resource
    public function edit(Brand $brand): View
    {
        return view('admin.brand.edit', compact('brand'));
    }

    // Update the specified resource in storage
    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Update image using ImageService
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->updateImage(
                $request->file('image'),
                'brands',
                $brand->uuid,
                $brand->image
            );
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->brandRepository->update($brand, $validated);

        return redirect()->route('admin.brands.index')
                        ->with('success', 'Brand updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(Brand $brand): RedirectResponse
    {
        // Delete image using ImageService
        if ($brand->image) {
            $this->imageService->deleteImage($brand->image);
        }

        $this->brandRepository->delete($brand);

        return redirect()->route('admin.brands.index')
                        ->with('success', 'Brand deleted successfully!');
    }
}
