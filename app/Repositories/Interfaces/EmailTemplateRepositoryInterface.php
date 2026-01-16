<?php

namespace App\Repositories\Interfaces;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmailTemplateRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?EmailTemplate;

    public function findByUuid(string $uuid): ?EmailTemplate;

    public function findBySlug(string $slug): ?EmailTemplate;

    public function create(array $data): EmailTemplate;

    public function update(EmailTemplate $emailTemplate, array $data): EmailTemplate;

    public function delete(EmailTemplate $emailTemplate): bool;

    public function getActive(): Collection;

    public function getInactive(): Collection;

    public function getByCategory(string $category): Collection;

    public function search(string $term): Collection;
}
