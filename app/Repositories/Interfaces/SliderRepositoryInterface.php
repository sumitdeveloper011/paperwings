<?php

namespace App\Repositories\Interfaces;

use App\Models\Slider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SliderRepositoryInterface
{
    // Get all sliders
    public function all(): Collection;

    // Get paginated sliders
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    // Find slider by ID
    public function find(int $id): ?Slider;

    // Find slider by UUID
    public function findByUuid(string $uuid): ?Slider;

    // Create new slider
    public function create(array $data): Slider;

    // Update slider
    public function update(Slider $slider, array $data): Slider;

    // Delete slider
    public function delete(Slider $slider): bool;

    // Get active sliders ordered by sort_order
    public function getActive(): Collection;

    // Get inactive sliders
    public function getInactive(): Collection;

    // Get sliders ordered by sort_order
    public function getOrdered(): Collection;

    // Update slider status
    public function updateStatus(Slider $slider, string $status): Slider;

    // Update sort order
    public function updateSortOrder(Slider $slider, int $sortOrder): Slider;

    // Move slider up in order
    public function moveUp(Slider $slider): bool;

    // Move slider down in order
    public function moveDown(Slider $slider): bool;

    // Move slider to specific position
    public function moveToPosition(Slider $slider, int $position): bool;

    // Get next sort order
    public function getNextSortOrder(): int;

    // Reorder all sliders
    public function reorderSliders(array $sliderIds): bool;
}
