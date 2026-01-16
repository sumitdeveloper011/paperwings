<?php

namespace App\Repositories;

use App\Models\EmailTemplate;
use App\Repositories\Interfaces\EmailTemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailTemplateRepository implements EmailTemplateRepositoryInterface
{
    protected EmailTemplate $model;

    public function __construct(EmailTemplate $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['creator', 'updater'])->orderBy('category')->orderBy('name')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['creator', 'updater'])
            ->orderBy('category')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function find(int $id): ?EmailTemplate
    {
        return $this->model->with(['variables', 'creator', 'updater'])->find($id);
    }

    public function findByUuid(string $uuid): ?EmailTemplate
    {
        return $this->model->with(['variables', 'creator', 'updater'])
            ->where('uuid', $uuid)
            ->first();
    }

    public function findBySlug(string $slug): ?EmailTemplate
    {
        return $this->model->with(['variables', 'creator', 'updater'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    public function create(array $data): EmailTemplate
    {
        $template = $this->model->create($data);
        return $template->load(['variables', 'creator', 'updater']);
    }

    public function update(EmailTemplate $emailTemplate, array $data): EmailTemplate
    {
        $emailTemplate->update($data);
        return $emailTemplate->load(['variables', 'creator', 'updater'])->fresh();
    }

    public function delete(EmailTemplate $emailTemplate): bool
    {
        return $emailTemplate->delete();
    }

    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function getInactive(): Collection
    {
        return $this->model->where('is_active', false)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    public function getByCategory(string $category): Collection
    {
        return $this->model->where('category', $category)
            ->orderBy('name')
            ->get();
    }

    public function search(string $term): Collection
    {
        return $this->model->where(function($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('slug', 'LIKE', "%{$term}%")
              ->orWhere('subject', 'LIKE', "%{$term}%");
        })->orderBy('name')->get();
    }
}
