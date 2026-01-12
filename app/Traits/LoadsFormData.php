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
        return [
            'categories' => Category::active()->ordered()->get(),
            'products' => Product::active()->orderBy('name')->get(),
            'bundles' => Product::bundles()->where('status', 1)->orderBy('name')->get(),
            'pages' => Page::where('status', 1)->orderBy('title')->get(),
        ];
    }
}
