<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $viewPath = 'frontend.home.';

    public function index(){
        $title = 'Home';
        $sliders = Slider::active()->ordered()->get();

        // Get categories that have at least 6 active products
        $categories = Category::active()
            ->ordered()
            ->take(6)
            ->get();

        // Get products by product type (random order for variety on each page load)
        $featuredProducts = Product::active()
            ->where('product_type', '1') // Featured
            ->inRandomOrder()
            ->take(18)
            ->get();
        $onSaleProducts = Product::active()
            ->where('product_type', '2') // On Sale
            ->inRandomOrder()
            ->take(18)
            ->get();

        $topRatedProducts = Product::active()
            ->where('product_type', '3') // Top Rated
            ->inRandomOrder()
            ->take(18)
            ->get();

        // Fallback: If no products by type, get general products
        $products = Product::active()->inRandomOrder()->take(18)->get();

        // Get random categories that have at least 6 active products
        $randomCategories = Category::active()
            ->withCount(['products' => function($query) {
                $query->where('status', 1);
            }])
            ->having('products_count', '>=', 6) // At least 6 active products
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Get products for each category
        $categoryProducts = [];
        foreach ($randomCategories as $category) {
            $categoryProducts[$category->id] = Product::active()
                ->where('category_id', $category->id)
                ->inRandomOrder()
                ->take(12) // Show 12 products per category
                ->get();
        }

        return view($this->viewPath . 'index', compact(
            'title',
            'sliders',
            'categories',
            'randomCategories',
            'categoryProducts',
            'products',
            'featuredProducts',
            'onSaleProducts',
            'topRatedProducts'
        ));
    }
}
