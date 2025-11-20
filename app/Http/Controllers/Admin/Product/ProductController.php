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
                        'category_id' => $product['CategoryId'] ? $this->categoryRepository->getByEposnowCategoryId($product['CategoryId'])?->id : 17,
                        'brand_id' => null,
                        'eposnow_product_id' => $product['Id'],
                        'eposnow_category_id' => $product['CategoryId'],
                        'eposnow_brand_id' => $product['BrandId'],
                        'barcode' => $product['Barcode'] ?? null,
                        'stock' => null,
                        'product_type' => null,
                        'name' => $product['Name'],
                        'slug' => Str::slug($product['Name']),
                        'total_price' => $product['SalePrice'] ?? 0.00,
                        'discount_price' => null,
                        'description' => $product['Description'] ?? null,
                        'short_description' => null,
                        'status' => 1,
                    ];

                    if ($product['Id']) {
                        if (Product::where('slug', $arr['slug'])->exists()) {
                            $slug = $arr['slug'].'-'.$arr['slug'];
                            $arr['slug'] = $slug;
                        }
                        $existing = Product::where('eposnow_product_id', $product['Id'])->first();
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

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = $this->categoryRepository->getActive();
        $brands = $this->brandRepository->all();

        return view('admin.product.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Handle multiple image uploads - store paths temporarily
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $imagePaths[] = $imagePath;
            }
        }

        // Remove images and accordion_data from validated as we'll save them separately
        unset($validated['images']);
        $accordionData = $validated['accordion_data'] ?? null;
        unset($validated['accordion_data']);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Create the product
        $product = $this->productRepository->create($validated);

        // Save images to separate table
        if (! empty($imagePaths)) {
            foreach ($imagePaths as $imagePath) {
                ProductImage::create([
                    'uuid' => Str::uuid(),
                    'product_id' => $product->id,
                    'eposnow_product_id' => $product->eposnow_product_id ?? $product->id, // Use product_id if eposnow_product_id is null
                    'image' => $imagePath,
                ]);
            }
        }

        // Save accordion data to separate table
        if ($accordionData && is_array($accordionData)) {
            foreach ($accordionData as $item) {
                if (! empty($item['heading']) && ! empty($item['content'])) {
                    ProductAccordion::create([
                        'uuid' => Str::uuid(),
                        'product_id' => $product->id,
                        'eposnow_product_id' => $product->eposnow_product_id ?? $product->id, // Use product_id if eposnow_product_id is null
                        'heading' => $item['heading'],
                        'content' => $item['content'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'images', 'accordions']);
        return view('admin.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $product->load(['category', 'brand', 'images', 'accordions']);
        $categories = $this->categoryRepository->getActive();
        $brands = $this->brandRepository->all();

        return view('admin.product.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        // Handle multiple image uploads - store paths temporarily
        $newImagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = Str::uuid().'.'.$image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                $newImagePaths[] = $imagePath;
            }
        }

        // Remove images and accordion_data from validated as we'll save them separately
        unset($validated['images']);
        $accordionData = $validated['accordion_data'] ?? null;
        unset($validated['accordion_data']);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Update the product
        $this->productRepository->update($product, $validated);

        // Handle images update
        if (! $request->boolean('keep_existing_images')) {
            // Delete old images from database and storage
            $oldImages = $product->images;
            foreach ($oldImages as $oldImage) {
                // Delete from storage
                if (Storage::disk('public')->exists($oldImage->image)) {
                    Storage::disk('public')->delete($oldImage->image);
                }
                // Delete from database
                $oldImage->delete();
            }
        }

        // Add new images to separate table
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

        // Handle accordion data update
        // Delete existing accordions first
        $product->accordions()->delete();

        // Save new accordion data to separate table
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Delete images from database and storage
        $images = $product->images;
        foreach ($images as $image) {
            if (Storage::disk('public')->exists($image->image)) {
                Storage::disk('public')->delete($image->image);
            }
            $image->delete();
        }

        // Delete accordions from database
        $product->accordions()->delete();

        // Delete the product
        $this->productRepository->delete($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Update product status
     */
    public function updateStatus(UpdateProductStatusRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

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


