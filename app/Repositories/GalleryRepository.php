<?php

namespace App\Repositories;

use App\Models\Gallery;
use App\Repositories\Interfaces\GalleryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GalleryRepository implements GalleryRepositoryInterface
{
    protected Gallery $model;

    public function __construct(Gallery $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['creator', 'coverImage', 'items'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['creator', 'coverImage', 'items'])
            ->orderBy('category')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function find(int $id): ?Gallery
    {
        return $this->model->with(['creator', 'coverImage', 'items'])->find($id);
    }

    public function findByUuid(string $uuid): ?Gallery
    {
        return $this->model->with(['creator', 'coverImage', 'items'])
            ->where('uuid', $uuid)
            ->first();
    }

    public function findBySlug(string $slug): ?Gallery
    {
        return $this->model->with(['creator', 'coverImage', 'items'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();
    }

    public function create(array $data): Gallery
    {
        $gallery = $this->model->create($data);
        return $gallery->load(['creator', 'coverImage', 'items']);
    }

    public function update(Gallery $gallery, array $data): Gallery
    {
        $gallery->update($data);
        return $gallery->load(['creator', 'coverImage', 'items'])->fresh();
    }

    public function delete(Gallery $gallery): bool
    {
        return $gallery->delete();
    }

    public function getActive(): Collection
    {
        return $this->model->where('status', 'active')
            ->with(['creator', 'coverImage', 'items'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function getInactive(): Collection
    {
        return $this->model->where('status', 'inactive')
            ->with(['creator', 'coverImage', 'items'])
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function getByCategory(string $category): Collection
    {
        return $this->model->where('category', $category)
            ->where('status', 'active')
            ->with(['creator', 'coverImage', 'items'])
            ->orderBy('name')
            ->get();
    }

    public function search(string $term): Collection
    {
        return $this->model->where(function($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('slug', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%");
        })
        ->with(['creator', 'coverImage', 'items'])
        ->orderBy('name')
        ->get();
    }
}
