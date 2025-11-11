<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;
use App\Services\EposNowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function getProductsForEposNow(Request $request): RedirectResponse
    {
        try{
            for($i = 1; $i <= 53; $i++) {
                $products = $this->eposNow->getProducts($i);
                foreach ($products as $product) {
                    $arr = [
                        'uuid' => Str::uuid(),
                        'product_id' => $product['Id'],
                        'category_id' => $product['CategoryId'],
                        'brand_id' => $product['BrandId'],
                        'name' => $product['Name'],
                        'slug' => Str::slug($product['Name']),
                        'total_price' => $product['SalePrice'] ?? 0.00,
                        'description' => $product['Description'] ?? null,
                        'short_description' => null,
                        'barcode' => $product['Barcode'] ?? null,
                        'accordion_data' => null,
                        'images' => $product['ProductImages'] ?? null,
                        'status' => 1,
                    ];

                    if ($product['CategoryId']) {
                        if (Product::where('slug', $arr['slug'])->exists()) {
                            $slug = $arr['slug'].'-'.$arr['slug'];
                            $arr['slug'] = $slug;
                        }
                        $existing = Product::where('product_id', $product['Id'])->first();
                        if (! $existing) {
                            $this->productRepository->create($arr);
                        }
                    }
                }
            }
            return redirect()->route('admin.products.index')
                    ->with('success', 'Products imported successfully from EposNow!');
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Failed to import products: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $subCategoryId = $request->get('subcategory_id');
        $brandId = $request->get('brand_id');

        if ($search) {
            $products = $this->productRepository->search($search);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $products->count(),
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } elseif ($categoryId) {
            $products = $this->productRepository->getByCategory($categoryId);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $products->count(),
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } elseif ($subCategoryId) {
            $products = $this->productRepository->getBySubCategory($subCategoryId);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $products->count(),
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } elseif ($brandId) {
            $products = $this->productRepository->getByBrand($brandId);
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $products->count(),
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $products = $this->productRepository->paginate(10);
        }

        $categories = $this->categoryRepository->getActive();
        $subCategories = $this->subCategoryRepository->getActive();
        $brands = $this->brandRepository->all();
        return view('admin.product.index', compact(
            'products', 'search', 'categories', 'subCategories', 'brands',
            'categoryId', 'subCategoryId', 'brandId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = $this->categoryRepository->getActive();
        $subCategories = $this->subCategoryRepository->getActive();
        $brands = $this->brandRepository->getActive();

        return view('admin.product.create', compact('categories', 'subCategories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255|unique:products,name',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'total_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'accordion_data' => 'nullable|array',
            'accordion_data.*.heading' => 'required_with:accordion_data|string|max:255',
            'accordion_data.*.content' => 'required_with:accordion_data|string',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:1,0',
        ]);

        // Handle multiple image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $imagePaths[] = $imagePath;
            }
        }
        $validated['images'] = ! empty($imagePaths) ? $imagePaths : null;

        // Process accordion data
        if ($request->filled('accordion_data')) {
            $accordionData = [];
            foreach ($validated['accordion_data'] as $item) {
                if (! empty($item['heading']) && ! empty($item['content'])) {
                    $accordionData[] = [
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ];
                }
            }
            $validated['accordion_data'] = ! empty($accordionData) ? $accordionData : null;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->productRepository->create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'subCategory', 'brand']);

        return view('admin.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $product->load(['category', 'subCategory', 'brand']);
        $categories = $this->categoryRepository->getActive();
        $subCategories = $this->subCategoryRepository->getActive();
        $brands = $this->brandRepository->all();

        return view('admin.product.edit', compact('product', 'categories', 'subCategories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255|unique:products,name,'.$product->id,
            'slug' => 'nullable|string|max:255|unique:products,slug,'.$product->id,
            'total_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'accordion_data' => 'nullable|array',
            'accordion_data.*.heading' => 'required_with:accordion_data|string|max:255',
            'accordion_data.*.content' => 'required_with:accordion_data|string',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:1,0',
            'keep_existing_images' => 'nullable|boolean',
        ]);

        // Handle multiple image uploads
        $imagePaths = [];

        // Keep existing images if requested
        if ($request->boolean('keep_existing_images') && $product->images) {
            $imagePaths = $product->images;
        }

        // Add new images
        if ($request->hasFile('images')) {
            // Delete old images if not keeping them
            if (! $request->boolean('keep_existing_images') && $product->images) {
                foreach ($product->images as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage)) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }
            }

            foreach ($request->file('images') as $image) {
                $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $imagePaths[] = $imagePath;
            }
        }

        $validated['images'] = ! empty($imagePaths) ? $imagePaths : null;

        // Process accordion data
        if ($request->filled('accordion_data')) {
            $accordionData = [];
            foreach ($validated['accordion_data'] as $item) {
                if (! empty($item['heading']) && ! empty($item['content'])) {
                    $accordionData[] = [
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ];
                }
            }
            $validated['accordion_data'] = ! empty($accordionData) ? $accordionData : null;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->productRepository->update($product, $validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Delete images if they exist
        if ($product->images) {
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        $this->productRepository->delete($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Update product status
     */
    public function updateStatus(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:1,0',
        ]);

        $this->productRepository->updateStatus($product, $validated['status']);

        return redirect()->back()
            ->with('success', 'Product status updated successfully!');
    }

    /**
     * Get subcategories by category (AJAX)
     */
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

    public function saveEposNowProductImage()
    {
        try {
            //$productId = '28002347';

            $productId = '28028587';
            // --- Step 1: API details ---
            $apiUrl = "https://api.eposnowhq.com/api/v4/ProductImage/{$productId}";
            $authHeader = 'Basic Wlc3QzBNSDAzSEZHUDhLU041MVdQVlBISU02MVdBVE46Tk9QWDNXSkoyVTZZTUZRNDJZNDJMMzI2OUcwQ0hTTU4=';
    
            // --- Step 2: Get image metadata from Epos Now ---
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $authHeader,
            ])->get($apiUrl);
    
            if (!$response->successful()) {
                Log::error("EposNow API error ({$response->status()}): ".$response->body());
                return null;
            }
    
            $data = $response->json();
            if (empty($data['ImageUrls'])) {
                Log::info("No image found for Product ID {$productId}");
                return null;
            }
    
            // --- Step 3: pick the main image URL ---
            $mainImage = collect($data['ImageUrls'])->firstWhere('MainImage', true);
            if (!$mainImage || empty($mainImage['ImageUrl'])) {
                Log::info("Main image missing for Product ID {$productId}");
                return null;
            }
    
            $imageUrl = $mainImage['ImageUrl'];
    
            // --- Step 4: Stream the image safely to a file ---
            $filename = $productId.'_'.basename(parse_url($imageUrl, PHP_URL_PATH));
            $savePath = "public/eposnow_images/{$filename}";
            $localFullPath = storage_path('app/'.$savePath);
    
            // Make sure directory exists
            if (!is_dir(dirname($localFullPath))) {
                mkdir(dirname($localFullPath), 0775, true);
            }
    
            // Stream download (binary-safe)
            Http::sink($localFullPath)->get($imageUrl);
    
            // --- Step 5: Verify file & return public URL ---
            if (!file_exists($localFullPath) || filesize($localFullPath) === 0) {
                Log::warning("Downloaded image empty or missing for {$productId}");
                return null;
            }
    
            return Storage::url($savePath);
    
        } catch (\Throwable $e) {
            Log::error("EposNow image save failed: ".$e->getMessage());
            return null;
        }
    }
}
