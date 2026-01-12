<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BundleController extends Controller
{
    // Display bundles listing page
    public function index(Request $request): View
    {
        $title = 'Product Bundles';

        // Get sort parameter from request
        $sort = $request->get('sort', 'featured');

        // Query products with product_type = 4 (bundles)
        $query = Product::bundles()
            ->where('status', 1)
            ->with(['bundleProducts' => function($query) {
                $query->where('status', 1)
                      ->select('products.id', 'products.name', 'products.slug', 'products.total_price', 'products.discount_price', 'products.status');
            }, 'bundleProducts.images' => function($query) {
                $query->select('products_images.id', 'products_images.product_id', 'products_images.image')
                      ->orderBy('products_images.id')
                      ->limit(1);
            }, 'images']);

        // Apply sorting based on sort parameter
        switch ($sort) {
            case 'price_low_high':
                $query->orderBy('total_price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('total_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'featured':
            default:
                $query->orderBy('sort_order')->orderBy('name');
                break;
        }

        $bundles = $query->paginate(12)->appends($request->query());

        return view('frontend.bundle.index', compact('title', 'bundles', 'sort'));
    }

    // Display bundle detail page
    public function show($slug): View
    {
        $bundle = Product::bundles()
            ->where('slug', $slug)
            ->where('status', 1)
            ->with(['bundleProducts' => function($query) {
                $query->where('status', 1)
                      ->select('products.id', 'products.name', 'products.slug', 'products.total_price', 'products.discount_price', 'products.status', 'products.category_id', 'products.brand_id');
            }, 'bundleProducts.images' => function($query) {
                $query->select('products_images.id', 'products_images.product_id', 'products_images.image')
                      ->orderBy('products_images.id');
            }, 'bundleProducts.category' => function($query) {
                $query->select('categories.id', 'categories.name', 'categories.slug');
            }, 'images', 'accordions'])
            ->firstOrFail();

        // Get related products from bundle products' categories
        $relatedProducts = collect();
        if ($bundle->bundleProducts->count() > 0) {
            $categoryIds = $bundle->bundleProducts->pluck('category_id')->filter()->unique()->toArray();
            $bundleProductIds = $bundle->bundleProducts->pluck('id')->toArray();

            if (!empty($categoryIds)) {
                $relatedProducts = Product::whereIn('category_id', $categoryIds)
                    ->whereNotIn('id', $bundleProductIds)
                    ->products() // Exclude bundles
                    ->where('status', 1)
                    ->with(['images' => function($q) {
                        $q->select('id', 'product_id', 'image')->orderBy('id')->limit(1);
                    }])
                    ->limit(12)
                    ->get()
                    ->shuffle()
                    ->take(8);
            }
        }

        $title = $bundle->name;

        return view('frontend.bundle.show', compact('title', 'bundle', 'relatedProducts'));
    }
}
