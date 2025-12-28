<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Repositories\Interfaces\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BrandRepository implements BrandRepositoryInterface
{
    protected Brand $model;

    public function __construct(Brand $model)
    {
        $this->model = $model;
    }

    // Get all brands
    public function all(): Collection
    {
        return $this->model->orderBy('name')->get();
    }

    // Get paginated brands
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    // Find brand by ID
    public function find(int $id): ?Brand
    {
        return $this->model->find($id);
    }

    // Find brand by UUID
    public function findByUuid(string $uuid): ?Brand
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    // Find brand by slug
    public function findBySlug(string $slug): ?Brand
    {
        return $this->model->where('slug', $slug)->first();
    }

    // Create new brand
    public function create(array $data): Brand
    {
        return $this->model->create($data);
    }

    // Update brand
    public function update(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        return $brand->fresh();
    }

    // Delete brand
    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    // Search brands by name
    public function search(string $term): Collection
    {
        return $this->model->where('name', 'LIKE', "%{$term}%")
                          ->orWhere('slug', 'LIKE', "%{$term}%")
                          ->orderBy('name')
                          ->get();
    }

    // Get brands with image
    public function withImage(): Collection
    {
        return $this->model->whereNotNull('image')->orderBy('name')->get();
    }

    // Get brands without image
    public function withoutImage(): Collection
    {
        return $this->model->whereNull('image')->orderBy('name')->get();
    }

    // Get active brands
    public function getActive(): Collection
    {
        return $this->model->where('status', 1)->orderBy('name')->get();
    }
    
    // Get inactive brands
    public function getInactive(): Collection
    {
        return $this->model->where('status', 0)->orderBy('name')->get();
    }
}
