<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Product;
use App\Services\InstagramService;
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

        // Optimized: Single query with eager loading for all product types (using scopes)
        $allProducts = Product::active()
            ->whereIn('product_type', [1, 2, 3])
            ->withFirstImage()
            ->selectMinimal()
            ->get()
            ->shuffle() // Shuffle in memory instead of expensive DB inRandomOrder()
            ->groupBy('product_type');

        $featuredProducts = $allProducts->get(1, collect())->take(18);
        $onSaleProducts = $allProducts->get(2, collect())->take(18);
        $topRatedProducts = $allProducts->get(3, collect())->take(18);
        
        // Fallback: If no products by type, get general products (using scopes)
        $products = Product::active()
            ->withFirstImage()
            ->selectMinimal()
            ->inRandomOrder()
            ->take(18)
            ->get();

        // Get random categories that have at least 6 active products
        $randomCategories = Category::active()
            ->withCount(['products' => function($query) {
                $query->where('status', 1);
            }])
            ->having('products_count', '>=', 6) // At least 6 active products
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Optimized: Load all category products in single query with eager loading (using scopes)
        $categoryProducts = [];
        if ($randomCategories->isNotEmpty()) {
            $allCategoryProducts = Product::active()
                ->whereIn('category_id', $randomCategories->pluck('id'))
                ->withFirstImage()
                ->selectMinimal()
                ->get()
                ->shuffle()
                ->groupBy('category_id');

            foreach ($randomCategories as $category) {
                $categoryProducts[$category->id] = $allCategoryProducts
                    ->get($category->id, collect())
                    ->take(12);
            }
        }

        // Cache settings query
        $settings = \Illuminate\Support\Facades\Cache::remember('homepage_settings', 3600, function() {
            return \App\Models\Setting::pluck('value', 'key')->toArray();
        });
        $instagramLink = $settings['social_instagram'] ?? null;
        
        // Fetch Instagram posts if API is configured
        $instagramPosts = [];
        $instagramService = new InstagramService();
        if ($instagramService->isConfigured()) {
            $instagramPosts = $instagramService->getRecentMedia(6);
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
            'topRatedProducts',
            'instagramLink',
            'instagramPosts'
        ));
    }
}
