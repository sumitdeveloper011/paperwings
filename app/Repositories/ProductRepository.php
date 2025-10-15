<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    protected Product $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * Get all products
     */
    public function all(): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])->orderBy('name')->get();
    }

    /**
     * Get paginated products
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    /**
     * Find product by ID
     */
    public function find(int $id): ?Product
    {
        return $this->model->with(['category', 'subCategory', 'brand'])->find($id);
    }

    /**
     * Find product by UUID
     */
    public function findByUuid(string $uuid): ?Product
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->where('uuid', $uuid)
                          ->first();
    }

    /**
     * Find product by slug
     */
    public function findBySlug(string $slug): ?Product
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->where('slug', $slug)
                          ->first();
    }

    /**
     * Create new product
     */
    public function create(array $data): Product
    {
        $product = $this->model->create($data);
        return $product->load(['category', 'subCategory', 'brand']);
    }

    /**
     * Update product
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->load(['category', 'subCategory', 'brand'])->fresh();
    }

    /**
     * Delete product
     */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    /**
     * Get active products
     */
    public function getActive(): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->where('status', 1)
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get inactive products
     */
    public function getInactive(): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->where('status', 0)
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->byCategory($categoryId)
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get products by subcategory
     */
    public function getBySubCategory(int $subCategoryId): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->bySubCategory($subCategoryId)
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get products by brand
     */
    public function getByBrand(int $brandId): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->byBrand($brandId)
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Update product status
     */
    public function updateStatus(Product $product, string $status): Product
    {
        $product->update(['status' => $status]);
        return $product->load(['category', 'subCategory', 'brand'])->fresh();
    }

    /**
     * Search products by name
     */
    public function search(string $term): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->where('name', 'LIKE', "%{$term}%")
                          ->orWhere('slug', 'LIKE', "%{$term}%")
                          ->orWhere('description', 'LIKE', "%{$term}%")
                          ->orWhere('short_description', 'LIKE', "%{$term}%")
                          ->orWhereHas('category', function($query) use ($term) {
                              $query->where('name', 'LIKE', "%{$term}%");
                          })
                          ->orWhereHas('subCategory', function($query) use ($term) {
                              $query->where('name', 'LIKE', "%{$term}%");
                          })
                          ->orWhereHas('brand', function($query) use ($term) {
                              $query->where('name', 'LIKE', "%{$term}%");
                          })
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get products with images
     */
    public function withImages(): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->whereNotNull('images')
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get products without images
     */
    public function withoutImages(): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->whereNull('images')
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get products with their relationships
     */
    public function withRelationships(): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get products by price range
     */
    public function getByPriceRange(float $minPrice, float $maxPrice): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->whereBetween('total_price', [$minPrice, $maxPrice])
                          ->orderBy('total_price')
                          ->get();
    }

    /**
     * Get featured products (you can define your own logic)
     */
    public function getFeatured(): Collection
    {
        return $this->model->with(['category', 'subCategory', 'brand'])
                          ->active()
                          ->whereNotNull('images')
                          ->orderBy('created_at', 'desc')
                          ->limit(8)
                          ->get();
    }
}
