<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    private $viewPath = 'frontend.product.';

    // Display product page
    public function product($slug){
        try {
            $title = 'Product';
            if (!$slug) {
                return view('frontend.errors.404', [
                    'title' => '404 - Product Not Found',
                    'message' => 'Product not found. The product you are looking for does not exist.'
                ]);
            }
            $product = Product::where('slug', $slug)->first();
            if (!$product) {
                return view('frontend.errors.404', [
                    'title' => '404 - Product Not Found',
                    'message' => 'Product not found. The product you are looking for does not exist.'
                ]);
            }
            return view($this->viewPath . 'product', compact('title', 'product'));
        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => '404 - Error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Display product detail page
    public function productDetail($slug){
        try {
            $title = 'Product Detail';
            if (!$slug) {
                abort(404);
            }
            $product = Product::with([
                'images' => function($query) {
                    $query->select('id', 'product_id', 'image')->orderBy('id');
                },
                'accordions',
                'category:id,name,slug',
                'brand:id,name',
                'approvedReviews.user',
                'activeFaqs',
                'tags',
                'approvedQuestions.answers' => function($query) {
                    $query->where('status', 1)->orderBy('helpful_count', 'desc');
                }
            ])
            ->withCount(['approvedReviews', 'views'])
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

            \App\Models\ProductView::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'viewed_at' => now(),
            ]);

            $recentlyViewed = session('recently_viewed', []);
            if (!in_array($product->id, $recentlyViewed)) {
                array_unshift($recentlyViewed, $product->id);
                $recentlyViewed = array_slice($recentlyViewed, 0, 20); // Keep last 20
                session(['recently_viewed' => $recentlyViewed]);
            }

            $relatedProducts = Product::with([
                'images' => function($query) {
                    $query->select('id', 'product_id', 'image')
                          ->orderBy('id')
                          ->limit(1);
                }
            ])
            ->active()
            ->where('id', '!=', $product->id)
            ->where(function($query) use ($product) {
                $query->where('eposnow_category_id', $product->eposnow_category_id)
                      ->orWhere('brand_id', $product->brand_id)
                      ->orWhereBetween('total_price', [
                          $product->total_price * 0.7,
                          $product->total_price * 1.3
                      ]);
            })
            ->select('id', 'name', 'slug', 'total_price', 'discount_price', 'eposnow_category_id', 'status', 'brand_id')
            ->take(20) // Get more than needed, then shuffle
            ->get()
            ->shuffle()
            ->take(8);
            
            return view($this->viewPath . 'product-detail', compact('title', 'product', 'relatedProducts'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        } catch (\Exception $e) {
            \Log::error('Product detail error', ['error' => $e->getMessage(), 'slug' => $slug]);
            abort(404);
        }
    }

    public function productByCategory($slug, Request $request){
        try {
            $title = 'Products by Category';
            if (!$slug) {
                return view('frontend.errors.404', [
                    'title' => 'Category Not Found',
                    'message' => 'Category not found. The category you are looking for does not exist.'
                ]);
            }
            $category = Category::where('slug', $slug)->first();
            if (!$category) {
                return view('frontend.errors.404', [
                    'title' => 'Category Not Found',
                    'message' => 'Category not found. The category you are looking for does not exist.'
                ]);
            }

            // Get sort parameter from request
            $sort = $request->get('sort', 'featured');

            // Get price filter parameters
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');

            // Optimized: Use cached database aggregation for price range
            $cacheKey = 'price_range_category_' . $category->eposnow_category_id;
            $priceRange = Cache::remember($cacheKey, 3600, function () use ($category) {
                return Product::where('eposnow_category_id', $category->eposnow_category_id)
                    ->active()
                    ->selectRaw('
                        MIN(COALESCE(discount_price, total_price)) as min_price,
                        MAX(COALESCE(discount_price, total_price)) as max_price
                    ')
                    ->first();
            });

            $priceMin = floor($priceRange->min_price ?? 0);
            $priceMax = ceil(($priceRange->max_price ?? 0) / 10) * 10;

            // Build query
            $query = Product::where('eposnow_category_id', $category->eposnow_category_id)
                ->active();

            // Apply price filter if provided
            if ($minPrice !== null || $maxPrice !== null) {
                if ($minPrice !== null && $maxPrice !== null) {
                    // Filter by price range (using COALESCE to get effective price)
                    $query->whereRaw('COALESCE(discount_price, total_price) >= ?', [$minPrice])
                          ->whereRaw('COALESCE(discount_price, total_price) <= ?', [$maxPrice]);
                } elseif ($minPrice !== null) {
                    $query->whereRaw('COALESCE(discount_price, total_price) >= ?', [$minPrice]);
                } elseif ($maxPrice !== null) {
                    $query->whereRaw('COALESCE(discount_price, total_price) <= ?', [$maxPrice]);
                }
            }

            // Apply sorting based on sort parameter
            switch ($sort) {
                case 'price_low_high':
                    $query->orderByRaw('COALESCE(discount_price, total_price) ASC');
                    break;
                case 'price_high_low':
                    $query->orderByRaw('COALESCE(discount_price, total_price) DESC');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'featured':
                default:
                    // Featured: products with product_type = 1 (Featured)
                    $query->orderByRaw('CASE WHEN product_type = 1 THEN 0 ELSE 1 END')
                          ->orderBy('created_at', 'desc');
                    break;
            }

            $products = $query->paginate(12)->appends($request->query()); // 12 products per page

            if ($products->isEmpty()) {
                return view('frontend.errors.404', [
                    'title' => 'Product Not Found',
                    'message' => 'No products found in this category. The category you are looking for does not exist.'
                ]);
            }

            // Optimized: Get categories with product count using whereHas instead of having
            $cacheKey = 'categories_with_count_all';
            $categories = Cache::remember($cacheKey, 3600, function () {
                return Category::active()
                    ->whereHas('activeProducts')
                    ->withCount('activeProducts')
                    ->ordered()
                    ->get();
            });

            return view('frontend.category.category', compact('title', 'category', 'products', 'categories', 'sort', 'priceMin', 'priceMax', 'minPrice', 'maxPrice'));

        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => '404 - Error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function shop(Request $request){
        try {
            $title = 'Shop';

            // Get sort parameter from request
            $sort = $request->get('sort', 'featured');

            // Get price filter parameters
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');

            // Get category filter (support multiple categories)
            $categorySlug = $request->get('category');
            $categoriesFilter = $request->get('categories', []); // Array of category IDs

            // Get brand filter (support multiple brands)
            $brandsFilter = $request->get('brands', []); // Array of brand IDs


            // Optimized: Use cached database aggregation for price range
            $cacheKey = 'price_range_all_products';
            $priceRange = Cache::remember($cacheKey, 3600, function () {
                return Product::active()
                    ->selectRaw('
                        MIN(COALESCE(discount_price, total_price)) as min_price,
                        MAX(COALESCE(discount_price, total_price)) as max_price
                    ')
                    ->first();
            });

            $priceMin = floor($priceRange->min_price ?? 0);
            $priceMax = ceil(($priceRange->max_price ?? 0) / 10) * 10;

            // Build query
            $query = Product::active();

            // Apply category filter if provided (support both single and multiple)
            if ($categorySlug) {
                $category = Category::where('slug', $categorySlug)->first();
                if ($category) {
                    $query->where('eposnow_category_id', $category->eposnow_category_id);
                }
            } elseif (!empty($categoriesFilter) && is_array($categoriesFilter)) {
                // Multiple categories filter
                $categoryIds = Category::whereIn('id', $categoriesFilter)
                    ->pluck('eposnow_category_id')
                    ->filter()
                    ->toArray();
                if (!empty($categoryIds)) {
                    $query->whereIn('eposnow_category_id', $categoryIds);
                }
            }

            // Apply brand filter (multiple brands)
            if (!empty($brandsFilter) && is_array($brandsFilter)) {
                $query->whereIn('brand_id', $brandsFilter);
            }


            // Apply price filter if provided
            if ($minPrice !== null || $maxPrice !== null) {
                if ($minPrice !== null && $maxPrice !== null) {
                    $query->whereRaw('COALESCE(discount_price, total_price) >= ?', [$minPrice])
                          ->whereRaw('COALESCE(discount_price, total_price) <= ?', [$maxPrice]);
                } elseif ($minPrice !== null) {
                    $query->whereRaw('COALESCE(discount_price, total_price) >= ?', [$minPrice]);
                } elseif ($maxPrice !== null) {
                    $query->whereRaw('COALESCE(discount_price, total_price) <= ?', [$maxPrice]);
                }
            }

            // Apply sorting based on sort parameter
            switch ($sort) {
                case 'price_low_high':
                    $query->orderByRaw('COALESCE(discount_price, total_price) ASC');
                    break;
                case 'price_high_low':
                    $query->orderByRaw('COALESCE(discount_price, total_price) DESC');
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
                    $query->orderByRaw('CASE WHEN product_type = 1 THEN 0 ELSE 1 END')
                          ->orderBy('created_at', 'desc');
                    break;
            }

            // Optimized: Add category and brand eager loading for better performance
            $products = $query->with([
                    'images' => function($q) {
                        $q->select('id', 'product_id', 'image')->orderBy('id')->limit(1);
                    },
                    'category:id,name,slug',
                    'brand:id,name'
                ])
                ->selectMinimal()
                ->paginate(12)
                ->appends($request->query());

            // Optimized: Get categories with product count using whereHas instead of having
            $cacheKey = 'categories_with_count_sidebar';
            $categories = Cache::remember($cacheKey, 3600, function () {
                return Category::active()
                    ->whereHas('activeProducts')
                    ->withCount('activeProducts')
                    ->select('id', 'name', 'slug')
                    ->ordered()
                    ->get();
            });

            // Get brands with product count for filter
            $brands = \App\Models\Brand::where('status', 1)
                ->whereHas('activeProducts')
                ->withCount(['activeProducts'])
                ->orderBy('name')
                ->get();

            return view('frontend.shop.shop', compact(
                'title',
                'products',
                'categories',
                'brands',
                'sort',
                'priceMin',
                'priceMax',
                'minPrice',
                'maxPrice',
                'categorySlug',
                'categoriesFilter',
                'brandsFilter'
            ));

        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => '404 - Error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
