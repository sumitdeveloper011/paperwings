<?php

namespace App\Repositories\Interfaces;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{
    // Get all categories
    public function all(): Collection;

    // Get paginated categories
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    // Find category by ID
    public function find(int $id): ?Category;

    // Find category by UUID
    public function findByUuid(string $uuid): ?Category;

    // Find category by slug
    public function findBySlug(string $slug): ?Category;

    // Create new category
    public function create(array $data): Category;

    // Update category
    public function update(Category $category, array $data): Category;

    // Delete category
    public function delete(Category $category): bool;

    // Get active categories
    public function getActive(): Collection;

    // Get inactive categories
    public function getInactive(): Collection;

    // Update category status
    public function updateStatus(Category $category, string $status): Category;

    // Search categories by name
    public function search(string $term): Collection;

    // Get categories with image
    public function withImage(): Collection;

    // Get categories without image
    public function withoutImage(): Collection;

    // Get category by Eposnow category ID
    public function getByEposnowCategoryId(int $eposnowCategoryId): ?Category;
}
