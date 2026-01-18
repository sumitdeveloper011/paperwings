<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Helpers\CacheHelper;

class ProductController extends Controller
{
    protected $viewPath = 'frontend.product.';

    public function index(Request $request)
    {
        try {
            $title = 'Products';
            $query = Product::active();

            // Apply filters
            if ($request->has('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->has('brand')) {
                $query->where('brand_id', $request->brand);
            }

            // Apply sorting
            $sort = $request->get('sort', 'name');
            switch ($sort) {
                case 'price_low':
                    $query->orderBy('total_price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('total_price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }

            $products = $query->paginate(12);

            return view($this->viewPath . 'index', compact('title', 'products'));
        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => '404 - Error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display product detail page
     * Alias method for productDetail() to maintain backward compatibility
     */
    public function productDetail($slug)
    {
        return $this->show($slug);
    }

    /**
     * Display product detail page
     */
    public function show($slug)
    {
        try {
            $product = Product::where('slug', $slug)
                ->active()
                ->with(['images' => function($query) {
                    $query->orderBy('id', 'asc');
                }, 'category', 'brand', 'tags', 'approvedReviews', 'activeFaqs', 'accordions'])
                ->firstOrFail();
            
            // Set page title using product meta_title or name
            $title = $product->meta_title ?? $product->name ?? 'Product Details';

            // If product is a bundle (product_type == 4), load bundleProducts
            if ($product->product_type == 4) {
                $product->load([
                    'bundleProducts' => function($query) {
                        $query->where('status', 1)
                              ->select('products.id', 'products.uuid', 'products.name', 'products.slug', 'products.total_price', 'products.discount_price', 'products.status', 'products.category_id', 'products.brand_id');
                    },
                    'bundleProducts.images' => function($query) {
                        $query->select('products_images.id', 'products_images.product_id', 'products_images.image')
                              ->orderBy('products_images.id');
                    },
                    'bundleProducts.category' => function($query) {
                        $query->select('categories.id', 'categories.name', 'categories.slug');
                    }
                ]);
            }

            // Track product view
            if (!session()->has('viewed_products')) {
                session(['viewed_products' => []]);
            }

            $viewedProducts = session('viewed_products', []);
            if (!in_array($product->id, $viewedProducts)) {
                $viewedProducts[] = $product->id;
                session(['viewed_products' => $viewedProducts]);

                ProductView::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'session_id' => session()->getId(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            // Get related products
            $relatedProducts = collect();
            
            if ($product->product_type == 4) {
                // For bundles: get related products from bundle products' categories
                if ($product->bundleProducts && $product->bundleProducts->count() > 0) {
                    $categoryIds = $product->bundleProducts->pluck('category_id')->filter()->unique()->toArray();
                    $bundleProductIds = $product->bundleProducts->pluck('id')->toArray();

                    if (!empty($categoryIds)) {
                        $relatedProducts = Product::whereIn('category_id', $categoryIds)
                            ->whereNotIn('id', $bundleProductIds)
                            ->where('id', '!=', $product->id)
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
            } else {
                // For regular products: get related products from same category
                if ($product->category_id) {
                    $relatedProducts = Product::where('category_id', $product->category_id)
                        ->where('id', '!=', $product->id)
                        ->active()
                        ->with(['images' => function($q) {
                            $q->select('id', 'product_id', 'image')->orderBy('id')->limit(1);
                        }])
                        ->limit(8)
                        ->get()
                        ->shuffle()
                        ->take(8);
                }
            }

            return view($this->viewPath . 'product-detail', compact('title', 'product', 'relatedProducts'));
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Exception $e) {
            Log::error('Product detail error', ['error' => $e->getMessage(), 'slug' => $slug]);
            abort(404);
        }
    }

    /**
     * Display products by category
     * Alias method for showCategory() to maintain backward compatibility
     */
    public function showCategory($slug, Request $request)
    {
        return $this->productByCategory($slug, $request);
    }

    /**
     * Display products by category
     */
    public function productByCategory($slug, Request $request){
        try {
            if (!$slug) {
                return view('frontend.errors.404', [
                    'title' => 'Category Not Found',
                    'message' => 'Category not found. The category you are looking for does not exist.'
                ]);
            }
            $category = Category::where('slug', $slug)
                ->where('status', 1)
                ->first();
            if (!$category) {
                return view('frontend.errors.404', [
                    'title' => 'Category Not Found',
                    'message' => 'Category not found. The category you are looking for does not exist.'
                ]);
            }

            // Set title to category name
            $title = $category->name;

            // Get sort parameter from request
            $sort = $request->get('sort', 'featured');

            // Get price filter parameters
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');

            // Optimized: Use cached database aggregation for price range
            $cacheKey = CacheHelper::getPriceRangeCacheKey($category->id);
            $priceRange = Cache::remember($cacheKey, 3600, function () use ($category) {
                return Product::where('category_id', $category->id)
                    ->active()
                    ->selectRaw('
                        MIN(COALESCE(discount_price, total_price)) as min_price,
                        MAX(COALESCE(discount_price, total_price)) as max_price
                    ')
                    ->first();
            });

            $priceMin = floor($priceRange->min_price ?? 0);
            $priceMax = ceil(($priceRange->max_price ?? 0) / 10) * 10;

            // Build query - use category_id instead of eposnow_category_id
            $query = Product::where('category_id', $category->id)
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

            // Eager load images for products
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

            // Optimized: Get categories with product count using category_id
            $cacheKey = CacheHelper::CATEGORIES_WITH_COUNT_ALL;
            $categories = Cache::remember($cacheKey, 3600, function () {
                $categories = Category::where('categories.status', 1)
                    ->select('categories.id', 'categories.name', 'categories.slug', 'categories.image')
                    ->leftJoin('products', function($join) {
                        $join->on('products.category_id', '=', 'categories.id')
                             ->where('products.status', '=', 1)
                             ->whereNull('products.deleted_at');
                    })
                    ->groupBy('categories.id', 'categories.name', 'categories.slug', 'categories.image')
                    ->selectRaw('COUNT(products.id) as active_products_count')
                    ->orderBy('categories.name', 'asc')
                    ->get();

                // Ensure count is an integer
                return $categories->map(function($category) {
                    $category->active_products_count = (int) $category->active_products_count;
                    return $category;
                });
            });

            $subtitle = 'Browse products in ' . $category->name;

            return view('frontend.category.category', compact('title', 'category', 'products', 'categories', 'sort', 'priceMin', 'priceMax', 'minPrice', 'maxPrice', 'subtitle'));

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

            // Get tags filter (support multiple tags)
            $tagsFilter = $request->get('tags', []); // Array of tag IDs

            // Validate array inputs to ensure they contain only integers (security: prevent injection)
            if (!empty($categoriesFilter) && is_array($categoriesFilter)) {
                $categoriesFilter = array_filter(array_map('intval', $categoriesFilter), function($id) {
                    return $id > 0;
                });
            }

            if (!empty($brandsFilter) && is_array($brandsFilter)) {
                $brandsFilter = array_filter(array_map('intval', $brandsFilter), function($id) {
                    return $id > 0;
                });
            }

            if (!empty($tagsFilter) && is_array($tagsFilter)) {
                $tagsFilter = array_filter(array_map('intval', $tagsFilter), function($id) {
                    return $id > 0;
                });
            }

            // Optimized: Use cached database aggregation for price range
            $cacheKey = CacheHelper::getPriceRangeCacheKey();
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
                    $query->where('category_id', $category->id);
                }
            } elseif (!empty($categoriesFilter) && is_array($categoriesFilter)) {
                // Multiple categories filter - use category_id directly
                $query->whereIn('category_id', $categoriesFilter);
            }

            // Apply brand filter (multiple brands)
            if (!empty($brandsFilter) && is_array($brandsFilter)) {
                $query->whereIn('brand_id', $brandsFilter);
            }

            // Apply tags filter (multiple tags)
            if (!empty($tagsFilter) && is_array($tagsFilter)) {
                $query->whereHas('tags', function($q) use ($tagsFilter) {
                    $q->whereIn('tags.id', $tagsFilter);
                });
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

            // Optimized: Get categories with product count using LEFT JOIN
            $cacheKey = CacheHelper::CATEGORIES_WITH_COUNT_SIDEBAR;
            $categories = Cache::remember($cacheKey, 3600, function () {
                $categories = Category::where('categories.status', 1)
                    ->select('categories.id', 'categories.name', 'categories.slug', 'categories.image')
                    ->leftJoin('products', function($join) {
                        $join->on('products.category_id', '=', 'categories.id')
                             ->where('products.status', '=', 1)
                             ->whereNull('products.deleted_at');
                    })
                    ->groupBy('categories.id', 'categories.name', 'categories.slug', 'categories.image')
                    ->selectRaw('COUNT(products.id) as active_products_count')
                    ->orderBy('categories.name', 'asc')
                    ->get();

                // Ensure count is an integer
                return $categories->map(function($category) {
                    $category->active_products_count = (int) $category->active_products_count;
                    return $category;
                });
            });

            // Get brands with product count for filter
            $brands = Brand::where('status', 1)
                ->whereHas('activeProducts')
                ->withCount(['activeProducts'])
                ->orderBy('name')
                ->get();

            // Get tags with product count for filter
            $tags = Tag::whereHas('products', function($q) {
                    $q->active();
                })
                ->withCount(['products' => function($q) {
                    $q->active();
                }])
                ->orderBy('name')
                ->get();

            return view('frontend.shop.shop', compact(
                'title',
                'products',
                'categories',
                'brands',
                'tags',
                'sort',
                'priceMin',
                'priceMax',
                'minPrice',
                'maxPrice',
                'categorySlug',
                'categoriesFilter',
                'brandsFilter',
                'tagsFilter'
            ));

        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => '404 - Error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display bundles listing page
     */
    public function bundles(Request $request)
    {
        $title = 'Product Bundles';

        // Get sort parameter from request
        $sort = $request->get('sort', 'featured');

        // Query products with product_type = 4 (bundles)
        $query = Product::bundles()
            ->where('status', 1)
            ->with(['images' => function($query) {
                $query->select('products_images.id', 'products_images.product_id', 'products_images.image')
                      ->orderBy('products_images.id')
                      ->limit(1);
            }]);

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
}
