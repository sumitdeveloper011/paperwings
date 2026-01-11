<?php

namespace App\Repositories;

use App\Models\ProductBundle;
use App\Repositories\Interfaces\BundleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BundleRepository implements BundleRepositoryInterface
{
    protected ProductBundle $model;

    public function __construct(ProductBundle $model)
    {
        $this->model = $model;
    }

    // Get all bundles
    public function all(): Collection
    {
        return $this->model->with(['products', 'images'])
                          ->orderBy('name')
                          ->get();
    }

    // Get paginated bundles
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->withCount('products')
                          ->with('images')
                          ->ordered()
                          ->paginate($perPage);
    }

    // Find bundle by ID
    public function find(int $id): ?ProductBundle
    {
        return $this->model->with(['products', 'images'])->find($id);
    }

    // Find bundle by UUID
    public function findByUuid(string $uuid): ?ProductBundle
    {
        return $this->model->with(['products', 'images'])
                          ->where('uuid', $uuid)
                          ->first();
    }

    // Create new bundle
    public function create(array $data): ProductBundle
    {
        $bundle = $this->model->create($data);
        return $bundle->load(['products', 'images']);
    }

    // Update bundle
    public function update(ProductBundle $bundle, array $data): ProductBundle
    {
        $bundle->update($data);
        return $bundle->load(['products', 'images'])->fresh();
    }

    // Delete bundle (soft delete)
    public function delete(ProductBundle $bundle): bool
    {
        return $bundle->delete();
    }

    // Get active bundles
    public function getActive(): Collection
    {
        return $this->model->with(['products', 'images'])
                          ->where('status', 1)
                          ->orderBy('name')
                          ->get();
    }

    // Get inactive bundles
    public function getInactive(): Collection
    {
        return $this->model->with(['products', 'images'])
                          ->where('status', 0)
                          ->orderBy('name')
                          ->get();
    }

    // Update bundle status
    public function updateStatus(ProductBundle $bundle, int $status): ProductBundle
    {
        $bundle->update(['status' => $status]);
        return $bundle->load(['products', 'images'])->fresh();
    }

    // Search bundles by name or description
    public function search(string $term): Collection
    {
        return $this->model->with(['products', 'images'])
                          ->where('name', 'LIKE', "%{$term}%")
                          ->orWhere('description', 'LIKE', "%{$term}%")
                          ->orderBy('name')
                          ->get();
    }

    // Get bundles with their relationships
    public function withRelationships(): Collection
    {
        return $this->model->with(['products', 'images'])
                          ->orderBy('name')
                          ->get();
    }

    // Get only trashed (soft deleted) bundles
    public function getTrashed(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->onlyTrashed()
                          ->with(['products', 'images'])
                          ->orderBy('deleted_at', 'desc')
                          ->paginate($perPage);
    }

    // Restore soft deleted bundle
    public function restore(ProductBundle $bundle): bool
    {
        return $bundle->restore();
    }

    // Force delete (permanently delete) bundle
    public function forceDelete(ProductBundle $bundle): bool
    {
        return $bundle->forceDelete();
    }

    // Find bundle by UUID including trashed
    public function findByUuidWithTrashed(string $uuid): ?ProductBundle
    {
        return $this->model->withTrashed()
                          ->with(['products', 'images'])
                          ->where('uuid', $uuid)
                          ->first();
    }
}
