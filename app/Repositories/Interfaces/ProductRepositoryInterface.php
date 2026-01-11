<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    // Get all products
    public function all(): Collection;

    // Get paginated products
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    // Find product by ID
    public function find(int $id): ?Product;

    // Find product by UUID
    public function findByUuid(string $uuid): ?Product;

    // Find product by slug
    public function findBySlug(string $slug): ?Product;

    // Create new product
    public function create(array $data): Product;

    // Update product
    public function update(Product $product, array $data): Product;

    // Delete product
    public function delete(Product $product): bool;

    // Get active products
    public function getActive(): Collection;

    // Get inactive products
    public function getInactive(): Collection;

    // Get products by category
    public function getByCategory(int $categoryId): Collection;

    // Get products by subcategory
    public function getBySubCategory(int $subCategoryId): Collection;

    // Get products by brand
    public function getByBrand(int $brandId): Collection;

    // Update product status
    public function updateStatus(Product $product, string $status): Product;

    // Search products by name
    public function search(string $term): Collection;

    // Get products with images
    public function withImages(): Collection;

    // Get products without images
    public function withoutImages(): Collection;

    // Get products with their relationships
    public function withRelationships(): Collection;

    // Get products by price range
    public function getByPriceRange(float $minPrice, float $maxPrice): Collection;

    // Get featured products
    public function getFeatured(): Collection;

    // Get only trashed (soft deleted) products
    public function getTrashed(int $perPage = 10): LengthAwarePaginator;

    // Restore soft deleted product
    public function restore(Product $product): bool;

    // Force delete (permanently delete) product
    public function forceDelete(Product $product): bool;
}
