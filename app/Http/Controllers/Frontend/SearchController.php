<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    // Autocomplete search (AJAX)
    public function autocomplete(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return $this->jsonSuccess('Search autocomplete completed.', ['products' => []]);
        }

        // Cache key based on query (short TTL for search results)
        $cacheKey = 'search_autocomplete_' . md5(strtolower($query));
        
        $products = Cache::remember($cacheKey, 300, function() use ($query) {
            return Product::active()
                ->withFirstImage()
                ->with(['category:id,name,slug'])
                ->selectMinimal()
                ->where(function($q) use ($query) {
                    // Prioritize prefix matches (can use index)
                    $q->where('name', 'like', "{$query}%")
                      ->orWhere('slug', 'like', "{$query}%")
                      ->orWhere('name', 'like', "% {$query}%");
                })
                ->orderByRaw("
                    CASE 
                        WHEN name LIKE ? THEN 1
                        WHEN slug LIKE ? THEN 2
                        WHEN name LIKE ? THEN 3
                        ELSE 4
                    END
                ", ["{$query}%", "{$query}%", "% {$query}%"])
                ->limit(8)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'price' => (float) $product->total_price,
                        'discount_price' => $product->discount_price ? (float) $product->discount_price : null,
                        'image' => $product->images->first()?->image_url ?? asset('assets/images/placeholder.jpg'),
                        'category' => $product->category->name ?? '',
                        'url' => route('product.detail', $product->slug)
                    ];
                });
        });

        return $this->jsonSuccess('Search autocomplete completed.', ['products' => $products]);
    }

    // Render autocomplete results as HTML (for AJAX)
    public function renderAutocomplete(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return $this->jsonSuccess('Search autocomplete rendered.', ['html' => '']);
        }

        // Cache key based on query (short TTL for search results)
        $cacheKey = 'search_autocomplete_html_' . md5(strtolower($query));
        
        $result = Cache::remember($cacheKey, 300, function() use ($query) {
            // Get products with images and category
            $products = Product::active()
                ->withFirstImage()
                ->with(['category:id,name,slug'])
                ->selectMinimal()
                ->where(function($q) use ($query) {
                    // Prioritize prefix matches (can use index)
                    $q->where('name', 'like', "{$query}%")
                      ->orWhere('slug', 'like', "{$query}%")
                      ->orWhere('name', 'like', "% {$query}%");
                })
                ->orderByRaw("
                    CASE 
                        WHEN name LIKE ? THEN 1
                        WHEN slug LIKE ? THEN 2
                        WHEN name LIKE ? THEN 3
                        ELSE 4
                    END
                ", ["{$query}%", "{$query}%", "% {$query}%"])
                ->limit(8)
                ->get();

            $html = view('frontend.search.partials.autocomplete-items', [
                'products' => $products
            ])->render();

            return [
                'html' => $html,
                'count' => $products->count()
            ];
        });

        return $this->jsonSuccess('Search autocomplete rendered.', [
            'html' => $result['html'],
            'count' => $result['count']
        ]);
    }

    // Render search results as HTML (for header search dropdown)
    public function renderResults(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return $this->jsonSuccess('Search autocomplete rendered.', ['html' => '']);
        }

        // Cache key based on query (short TTL for search results)
        $cacheKey = 'search_results_html_' . md5(strtolower($query));
        
        $result = Cache::remember($cacheKey, 300, function() use ($query) {
            // Get products with images and category
            $products = Product::active()
                ->withFirstImage()
                ->with(['category:id,name,slug'])
                ->selectMinimal()
                ->where(function($q) use ($query) {
                    // Prioritize prefix matches (can use index)
                    $q->where('name', 'like', "{$query}%")
                      ->orWhere('slug', 'like', "{$query}%")
                      ->orWhere('name', 'like', "% {$query}%");
                })
                ->orderByRaw("
                    CASE 
                        WHEN name LIKE ? THEN 1
                        WHEN slug LIKE ? THEN 2
                        WHEN name LIKE ? THEN 3
                        ELSE 4
                    END
                ", ["{$query}%", "{$query}%", "% {$query}%"])
                ->limit(5)
                ->get();

            $html = view('frontend.search.partials.result-items', [
                'products' => $products
            ])->render();

            return [
                'html' => $html,
                'count' => $products->count()
            ];
        });

        return $this->jsonSuccess('Search autocomplete rendered.', [
            'html' => $result['html'],
            'count' => $result['count']
        ]);
    }

    // Full search results page
    public function index(Request $request): View
    {
        $searchQuery = trim($request->get('q', ''));
        $title = $searchQuery ? 'Search Results for "' . $searchQuery . '"' : 'Search';
        $category = $request->get('category');
        $sort = $request->get('sort', 'relevance');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $productsQuery = Product::active()
            ->withFirstImage()
            ->with(['category:id,name,slug'])
            ->selectMinimal();

        if ($searchQuery) {
            $productsQuery->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', "{$searchQuery}%")
                  ->orWhere('name', 'like', "% {$searchQuery}%")
                  ->orWhere('slug', 'like', "{$searchQuery}%");
                
                if (strlen($searchQuery) > 3) {
                    $q->orWhere('description', 'like', "%{$searchQuery}%")
                      ->orWhere('short_description', 'like', "%{$searchQuery}%");
                }
            });
        }

        if ($category) {
            $productsQuery->whereHas('category', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        if ($minPrice !== null) {
            $productsQuery->where('total_price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $productsQuery->where('total_price', '<=', $maxPrice);
        }

        // Map category page sort format to search format
        $sortMap = [
            'featured' => 'relevance',
            'price_low_high' => 'price_asc',
            'price_high_low' => 'price_desc',
            'name_asc' => 'name_asc',
            'name_desc' => 'name_desc',
            'newest' => 'newest'
        ];
        $actualSort = $sortMap[$sort] ?? $sort;

        switch ($actualSort) {
            case 'price_asc':
                $productsQuery->orderBy('total_price', 'asc');
                break;
            case 'price_desc':
                $productsQuery->orderBy('total_price', 'desc');
                break;
            case 'name_asc':
                $productsQuery->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $productsQuery->orderBy('name', 'desc');
                break;
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            default:
                if ($searchQuery) {
                    $productsQuery->orderByRaw("
                        CASE 
                            WHEN name LIKE ? THEN 1
                            WHEN name LIKE ? THEN 2
                            WHEN slug LIKE ? THEN 3
                            ELSE 4
                        END
                    ", ["{$searchQuery}%", "% {$searchQuery}%", "{$searchQuery}%"]);
                }
                $productsQuery->orderBy('name', 'asc');
                break;
        }

        $products = $productsQuery->paginate(20)->withQueryString();

        // Calculate price range for filter
        $priceRange = Product::active()
            ->selectRaw('
                MIN(COALESCE(discount_price, total_price)) as min_price,
                MAX(COALESCE(discount_price, total_price)) as max_price
            ')
            ->first();

        $priceMin = floor($priceRange->min_price ?? 0);
        $priceMax = ceil(($priceRange->max_price ?? 0) / 10) * 10;

        // Get categories with product count
        $categories = Category::where('categories.status', 1)
            ->select('categories.id', 'categories.name', 'categories.slug')
            ->leftJoin('products', function($join) {
                $join->on('products.category_id', '=', 'categories.id')
                     ->where('products.status', '=', 1);
            })
            ->groupBy('categories.id', 'categories.name', 'categories.slug')
            ->selectRaw('COUNT(products.id) as active_products_count')
            ->orderBy('categories.name', 'asc')
            ->get()
            ->map(function($category) {
                $category->active_products_count = (int) $category->active_products_count;
                return $category;
            });

        // Map sort values to match category page format for display
        $displaySortMap = [
            'relevance' => 'featured',
            'price_asc' => 'price_low_high',
            'price_desc' => 'price_high_low',
            'name_asc' => 'name_asc',
            'name_desc' => 'name_desc',
            'newest' => 'newest'
        ];
        $displaySort = $displaySortMap[$actualSort] ?? 'featured';

        $query = $searchQuery; // Keep 'query' variable for view compatibility

        return view('frontend.search.index', compact('title', 'products', 'query', 'category', 'sort', 'displaySort', 'minPrice', 'maxPrice', 'priceMin', 'priceMax', 'categories'));
    }
}

