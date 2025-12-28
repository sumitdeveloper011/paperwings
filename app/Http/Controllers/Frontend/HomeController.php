<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\SpecialOffersBanner;
use App\Models\Faq;
use App\Models\ProductBundle;
use App\Models\AboutSection;
use App\Services\InstagramService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $viewPath = 'frontend.home.';

    // Display homepage with products, categories, testimonials, and other content
    public function index(){
        $title = 'Home';
        $sliders = Slider::active()->ordered()->get();

        $categories = Category::active()
            ->ordered()
            ->take(6)
            ->get();

        $allProducts = Product::active()
            ->whereIn('product_type', [1, 2, 3])
            ->withFirstImage()
            ->selectMinimal()
            ->get()
            ->shuffle()
            ->groupBy('product_type');

        $featuredProducts = $allProducts->get(1, collect())->take(8);
        $onSaleProducts = $allProducts->get(2, collect())->take(8);
        $topRatedProducts = $allProducts->get(3, collect())->take(8);

        $newArrivals = Product::active()
            ->where('created_at', '>=', now()->subDays(30))
            ->withFirstImage()
            ->selectMinimal()
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        if ($newArrivals->isEmpty()) {
            $newArrivals = Product::where('status', 1)
                ->withFirstImage()
                ->selectMinimal()
                ->orderBy('id', 'desc')
                ->take(8)
                ->get();
        }

        $recentlyViewedIds = session('recently_viewed', []);
        $recentlyViewed = collect();
        if (!empty($recentlyViewedIds) && is_array($recentlyViewedIds)) {
            $recentlyViewed = Product::whereIn('id', $recentlyViewedIds)
                ->withFirstImage()
                ->selectMinimal()
                ->active()
                ->get()
                ->sortBy(function($product) use ($recentlyViewedIds) {
                    $index = array_search($product->id, $recentlyViewedIds);
                    return $index !== false ? $index : 9999;
                })
                ->take(8)
                ->values();
        }

        if ($recentlyViewed->isEmpty()) {
            $recentlyViewed = Product::active()
                ->withFirstImage()
                ->selectMinimal()
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get()
                ->shuffle()
                ->take(8);
        }

        if ($recentlyViewed->isEmpty()) {
            $recentlyViewed = Product::where('status', 1)
                ->withFirstImage()
                ->selectMinimal()
                ->orderBy('id', 'desc')
                ->take(8)
                ->get();
        }

        $products = Product::active()
            ->withFirstImage()
            ->selectMinimal()
            ->take(50)
            ->get()
            ->shuffle()
            ->take(18);

        $randomCategories = Category::active()
            ->whereHas('products', function($query) {
                $query->where('status', 1);
            })
            ->withCount(['products' => function($query) {
                $query->where('status', 1);
            }])
            ->take(10)
            ->get()
            ->shuffle()
            ->take(4);

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

        if ($randomCategories->isEmpty()) {
            $randomCategories = Category::where('status', 1)
                ->whereHas('products', function($query) {
                    $query->where('status', 1);
                })
                ->withCount(['products' => function($query) {
                    $query->where('status', 1);
                }])
                ->orderBy('name')
                ->take(4)
                ->get();

            if ($randomCategories->isNotEmpty()) {
                $allCategoryProducts = Product::where('status', 1)
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
        }

        $settings = \Illuminate\Support\Facades\Cache::remember('homepage_settings', 3600, function() {
            return \App\Models\Setting::pluck('value', 'key')->toArray();
        });
        $instagramLink = $settings['social_instagram'] ?? null;

        $instagramPosts = [];
        $instagramService = new InstagramService();
        if ($instagramService->isConfigured()) {
            $instagramPosts = $instagramService->getRecentMedia(6);
        }

        $testimonials = Testimonial::active()
            ->ordered()
            ->take(6)
            ->get();

        if ($testimonials->isEmpty()) {
            $testimonials = Testimonial::where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->take(6)
                ->get();
        }

        $specialOfferBanners = SpecialOffersBanner::active()
            ->ordered()
            ->get();

        $aboutSection = AboutSection::active()
            ->ordered()
            ->first();

        $faqs = Faq::active()
            ->ordered()
            ->take(10)
            ->get();

        $youMayAlsoLike = collect();
        if (!empty($recentlyViewedIds) && is_array($recentlyViewedIds)) {
            $recentCategoryIds = Product::whereIn('id', array_slice($recentlyViewedIds, 0, 5))
                ->select('category_id')
                ->distinct()
                ->pluck('category_id')
                ->filter()
                ->toArray();

            if (!empty($recentCategoryIds)) {
                $youMayAlsoLike = Product::active()
                    ->whereIn('category_id', $recentCategoryIds)
                    ->whereNotIn('id', $recentlyViewedIds)
                    ->withFirstImage()
                    ->selectMinimal()
                    ->take(20)
                    ->get()
                    ->shuffle()
                    ->take(8);
            }
        }

        if ($youMayAlsoLike->isEmpty()) {
            $youMayAlsoLike = Product::active()
                ->withFirstImage()
                ->selectMinimal()
                ->take(20)
                ->get()
                ->shuffle()
                ->take(8);
        }

        if ($youMayAlsoLike->isEmpty()) {
            $youMayAlsoLike = Product::where('status', 1)
                ->withFirstImage()
                ->selectMinimal()
                ->orderBy('id', 'desc')
                ->take(8)
                ->get();
        }

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
            ->take(6)
            ->get();

        if ($bundles->isEmpty()) {
            $bundles = ProductBundle::where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->with(['products' => function($query) {
                    $query->select('products.id', 'products.name', 'products.slug', 'products.total_price', 'products.discount_price', 'products.status')
                          ->where('products.status', 1);
                }, 'products.images' => function($query) {
                    $query->select('products_images.id', 'products_images.product_id', 'products_images.image')
                          ->orderBy('products_images.id')
                          ->limit(1);
                }])
                ->take(6)
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
            'topRatedProducts',
            'newArrivals',
            'recentlyViewed',
            'testimonials',
            'specialOfferBanners',
            'aboutSection',
            'faqs',
            'youMayAlsoLike',
            'bundles',
            'instagramLink',
            'instagramPosts'
        ));
    }
}
