<?php

namespace App\Repositories\Interfaces;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SubCategoryRepositoryInterface
{
    /**
     * Get all subcategories
     */
    public function all(): Collection;

    /**
     * Get paginated subcategories
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    /**
     * Find subcategory by ID
     */
    public function find(int $id): ?SubCategory;

    /**
     * Find subcategory by UUID
     */
    public function findByUuid(string $uuid): ?SubCategory;

    /**
     * Find subcategory by slug
     */
    public function findBySlug(string $slug): ?SubCategory;

    /**
     * Create new subcategory
     */
    public function create(array $data): SubCategory;

    /**
     * Update subcategory
     */
    public function update(SubCategory $subCategory, array $data): SubCategory;

    /**
     * Delete subcategory
     */
    public function delete(SubCategory $subCategory): bool;

    /**
     * Get active subcategories
     */
    public function getActive(): Collection;

    /**
     * Get inactive subcategories
     */
    public function getInactive(): Collection;

    /**
     * Get subcategories by category
     */
    public function getByCategory(int $categoryId): Collection;

    /**
     * Get active subcategories by category
     */
    public function getActiveByCategory(int $categoryId): Collection;

    /**
     * Update subcategory status
     */
    public function updateStatus(SubCategory $subCategory, string $status): SubCategory;

    /**
     * Search subcategories by name
     */
    public function search(string $term): Collection;

    /**
     * Get subcategories with image
     */
    public function withImage(): Collection;

    /**
     * Get subcategories without image
     */
    public function withoutImage(): Collection;

    /**
     * Get subcategories with their categories
     */
    public function withCategory(): Collection;
}
