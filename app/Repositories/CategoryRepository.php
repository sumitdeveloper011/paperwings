<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected Category $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * Get all categories
     */
    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    /**
     * Get paginated categories
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Find category by ID
     */
    public function find(int $id): ?Category
    {
        return $this->model->find($id);
    }

    /**
     * Find category by UUID
     */
    public function findByUuid(string $uuid): ?Category
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Create new category
     */
    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    /**
     * Update category
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    /**
     * Delete category
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * Get active categories
     */
    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('name')->get();
    }

    /**
     * Get inactive categories
     */
    public function getInactive(): Collection
    {
        return $this->model->inactive()->orderBy('name')->get();
    }

    /**
     * Update category status
     */
    public function updateStatus(Category $category, string $status): Category
    {
        $category->update(['status' => $status]);
        return $category->fresh();
    }

    /**
     * Search categories by name
     */
    public function search(string $term): Collection
    {
        return $this->model->where('name', 'LIKE', "%{$term}%")
                          ->orWhere('slug', 'LIKE', "%{$term}%")
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get categories with image
     */
    public function withImage(): Collection
    {
        return $this->model->whereNotNull('image')->orderBy('name')->get();
    }

    /**
     * Get categories without image
     */
    public function withoutImage(): Collection
    {
        return $this->model->whereNull('image')->orderBy('name')->get();
    }
}
