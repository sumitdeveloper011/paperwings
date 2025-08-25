<?php

namespace App\Repositories;

use App\Models\SubCategory;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    protected SubCategory $model;

    public function __construct(SubCategory $model)
    {
        $this->model = $model;
    }

    /**
     * Get all subcategories
     */
    public function all(): Collection
    {
        return $this->model->with('category')->orderBy('name')->get();
    }

    /**
     * Get paginated subcategories
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with('category')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find subcategory by ID
     */
    public function find(int $id): ?SubCategory
    {
        return $this->model->with('category')->find($id);
    }

    /**
     * Find subcategory by UUID
     */
    public function findByUuid(string $uuid): ?SubCategory
    {
        return $this->model->with('category')->where('uuid', $uuid)->first();
    }

    /**
     * Find subcategory by slug
     */
    public function findBySlug(string $slug): ?SubCategory
    {
        return $this->model->with('category')->where('slug', $slug)->first();
    }

    /**
     * Create new subcategory
     */
    public function create(array $data): SubCategory
    {
        $subCategory = $this->model->create($data);
        return $subCategory->load('category');
    }

    /**
     * Update subcategory
     */
    public function update(SubCategory $subCategory, array $data): SubCategory
    {
        $subCategory->update($data);
        return $subCategory->load('category')->fresh();
    }

    /**
     * Delete subcategory
     */
    public function delete(SubCategory $subCategory): bool
    {
        return $subCategory->delete();
    }

    /**
     * Get active subcategories
     */
    public function getActive(): Collection
    {
        return $this->model->with('category')->active()->orderBy('name')->get();
    }

    /**
     * Get inactive subcategories
     */
    public function getInactive(): Collection
    {
        return $this->model->with('category')->inactive()->orderBy('name')->get();
    }

    /**
     * Get subcategories by category
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->model->with('category')->byCategory($categoryId)->orderBy('name')->get();
    }

    /**
     * Get active subcategories by category
     */
    public function getActiveByCategory(int $categoryId): Collection
    {
        return $this->model->with('category')->byCategory($categoryId)->active()->orderBy('name')->get();
    }

    /**
     * Update subcategory status
     */
    public function updateStatus(SubCategory $subCategory, string $status): SubCategory
    {
        $subCategory->update(['status' => $status]);
        return $subCategory->load('category')->fresh();
    }

    /**
     * Search subcategories by name
     */
    public function search(string $term): Collection
    {
        return $this->model->with('category')
                          ->where('name', 'LIKE', "%{$term}%")
                          ->orWhere('slug', 'LIKE', "%{$term}%")
                          ->orWhereHas('category', function($query) use ($term) {
                              $query->where('name', 'LIKE', "%{$term}%");
                          })
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get subcategories with image
     */
    public function withImage(): Collection
    {
        return $this->model->with('category')->whereNotNull('image')->orderBy('name')->get();
    }

    /**
     * Get subcategories without image
     */
    public function withoutImage(): Collection
    {
        return $this->model->with('category')->whereNull('image')->orderBy('name')->get();
    }

    /**
     * Get subcategories with their categories
     */
    public function withCategory(): Collection
    {
        return $this->model->with('category')->orderBy('name')->get();
    }
}
