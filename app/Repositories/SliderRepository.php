<?php

namespace App\Repositories;

use App\Models\Slider;
use App\Repositories\Interfaces\SliderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SliderRepository implements SliderRepositoryInterface
{
    protected Slider $model;

    public function __construct(Slider $model)
    {
        $this->model = $model;
    }

    /**
     * Get all sliders
     */
    public function all(): Collection
    {
        return $this->model->ordered()->get();
    }

    /**
     * Get paginated sliders
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->ordered()->paginate($perPage);
    }

    /**
     * Find slider by ID
     */
    public function find(int $id): ?Slider
    {
        return $this->model->find($id);
    }

    /**
     * Find slider by UUID
     */
    public function findByUuid(string $uuid): ?Slider
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    /**
     * Create new slider
     */
    public function create(array $data): Slider
    {
        return $this->model->create($data);
    }

    /**
     * Update slider
     */
    public function update(Slider $slider, array $data): Slider
    {
        $slider->update($data);
        return $slider->fresh();
    }

    /**
     * Delete slider
     */
    public function delete(Slider $slider): bool
    {
        return $slider->delete();
    }

    /**
     * Get active sliders ordered by sort_order
     */
    public function getActive(): Collection
    {
        return $this->model->where('status', 1)->ordered()->get();
    }

    /**
     * Get inactive sliders
     */
    public function getInactive(): Collection
    {
        return $this->model->where('status', 0)->ordered()->get();
    }

    /**
     * Get sliders ordered by sort_order
     */
    public function getOrdered(): Collection
    {
        return $this->model->ordered()->get();
    }

    /**
     * Update slider status
     */
    public function updateStatus(Slider $slider, string $status): Slider
    {
        $slider->update(['status' => $status]);
        return $slider->fresh();
    }

    /**
     * Update sort order
     */
    public function updateSortOrder(Slider $slider, int $sortOrder): Slider
    {
        $slider->update(['sort_order' => $sortOrder]);
        return $slider->fresh();
    }

    /**
     * Move slider up in order
     */
    public function moveUp(Slider $slider): bool
    {
        try {
            $slider->moveUp();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Move slider down in order
     */
    public function moveDown(Slider $slider): bool
    {
        try {
            $slider->moveDown();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Move slider to specific position
     */
    public function moveToPosition(Slider $slider, int $position): bool
    {
        try {
            $slider->moveToPosition($position);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get next sort order
     */
    public function getNextSortOrder(): int
    {
        $maxOrder = $this->model->max('sort_order') ?? 0;
        return $maxOrder + 1;
    }

    /**
     * Reorder all sliders
     */
    public function reorderSliders(array $sliderIds): bool
    {
        try {
            foreach ($sliderIds as $index => $sliderId) {
                $this->model->where('id', $sliderId)->update(['sort_order' => $index + 1]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
