<?php

namespace App\Repositories\Interfaces;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface GalleryRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Gallery;

    public function findByUuid(string $uuid): ?Gallery;

    public function findBySlug(string $slug): ?Gallery;

    public function create(array $data): Gallery;

    public function update(Gallery $gallery, array $data): Gallery;

    public function delete(Gallery $gallery): bool;

    public function getActive(): Collection;

    public function getInactive(): Collection;

    public function getByCategory(string $category): Collection;

    public function search(string $term): Collection;
}
