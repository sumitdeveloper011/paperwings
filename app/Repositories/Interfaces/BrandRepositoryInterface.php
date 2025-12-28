<?php

namespace App\Repositories\Interfaces;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BrandRepositoryInterface
{
    // Get all brands
    public function all(): Collection;

    // Get paginated brands
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    // Find brand by ID
    public function find(int $id): ?Brand;

    // Find brand by UUID
    public function findByUuid(string $uuid): ?Brand;

    // Find brand by slug
    public function findBySlug(string $slug): ?Brand;

    // Create new brand
    public function create(array $data): Brand;

    // Update brand
    public function update(Brand $brand, array $data): Brand;

    // Delete brand
    public function delete(Brand $brand): bool;

    // Search brands by name
    public function search(string $term): Collection;

    // Get brands with image
    public function withImage(): Collection;

    // Get brands without image
    public function withoutImage(): Collection;
}
