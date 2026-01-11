<?php

namespace App\Repositories\Interfaces;

use App\Models\ProductBundle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BundleRepositoryInterface
{
    // Get all bundles
    public function all(): Collection;

    // Get paginated bundles
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    // Find bundle by ID
    public function find(int $id): ?ProductBundle;

    // Find bundle by UUID
    public function findByUuid(string $uuid): ?ProductBundle;

    // Create new bundle
    public function create(array $data): ProductBundle;

    // Update bundle
    public function update(ProductBundle $bundle, array $data): ProductBundle;

    // Delete bundle (soft delete)
    public function delete(ProductBundle $bundle): bool;

    // Get active bundles
    public function getActive(): Collection;

    // Get inactive bundles
    public function getInactive(): Collection;

    // Update bundle status
    public function updateStatus(ProductBundle $bundle, int $status): ProductBundle;

    // Search bundles by name or description
    public function search(string $term): Collection;

    // Get bundles with their relationships
    public function withRelationships(): Collection;

    // Get only trashed (soft deleted) bundles
    public function getTrashed(int $perPage = 10): LengthAwarePaginator;

    // Restore soft deleted bundle
    public function restore(ProductBundle $bundle): bool;

    // Force delete (permanently delete) bundle
    public function forceDelete(ProductBundle $bundle): bool;

    // Find bundle by UUID including trashed
    public function findByUuidWithTrashed(string $uuid): ?ProductBundle;
}
