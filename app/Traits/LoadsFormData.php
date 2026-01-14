<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;

trait LoadsFormData
{
    /**
     * Get common form data for admin controllers
     *
     * @return array
     */
    protected function getFormData(): array
    {
        try {
            return [
                'categories' => Category::active()->ordered()->get(),
                'products' => Product::active()
                    ->select('id', 'uuid', 'name', 'slug')
                    ->orderBy('name')
                    ->get(),
                'bundles' => Product::bundles()
                    ->where('status', 1)
                    ->select('id', 'uuid', 'name', 'slug')
                    ->orderBy('name')
                    ->get(),
                'pages' => Page::where('status', 1)
                    ->select('id', 'uuid', 'title', 'slug')
                    ->orderBy('title')
                    ->get(),
            ];
        } catch (\Exception $e) {
            return [
                'categories' => collect(),
                'products' => collect(),
                'bundles' => collect(),
                'pages' => collect(),
            ];
        }
    }
}
