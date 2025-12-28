<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ProductBundle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BundleController extends Controller
{
    // Display bundles listing page
    public function index(): View
    {
        $title = 'Product Bundles';
        $bundles = ProductBundle::active()
            ->ordered()
            ->with(['products' => function($query) {
                $query->select('products.id', 'products.name', 'products.slug', 'products.total_price', 'products.discount_price', 'products.status')
                      ->where('products.status', 1);
            }, 'products.images' => function($query) {
                $query->select('products_images.id', 'products_images.product_id', 'products_images.image')
                      ->orderBy('products_images.id')
                      ->limit(1);
            }])
            ->paginate(12);

        return view('frontend.bundle.index', compact('title', 'bundles'));
    }

    // Display bundle detail page
    public function show($slug): View
    {
        $bundle = ProductBundle::where('slug', $slug)
            ->active()
            ->with(['products' => function($query) {
                $query->select('products.id', 'products.name', 'products.slug', 'products.total_price', 'products.discount_price', 'products.status', 'products.category_id', 'products.brand_id')
                      ->where('products.status', 1);
            }, 'products.images' => function($query) {
                $query->select('products_images.id', 'products_images.product_id', 'products_images.image')
                      ->orderBy('products_images.id');
            }, 'products.category' => function($query) {
                $query->select('categories.id', 'categories.name', 'categories.slug');
            }])
            ->firstOrFail();

        $title = $bundle->name;

        return view('frontend.bundle.show', compact('title', 'bundle'));
    }
}
