<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
            return response()->json(['products' => []]);
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

        return response()->json(['products' => $products]);
    }

    // Render autocomplete results as HTML (for AJAX)
    public function renderAutocomplete(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'html' => ''
            ]);
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

        return response()->json([
            'success' => true,
            'html' => $result['html'],
            'count' => $result['count']
        ]);
    }

    // Render search results as HTML (for header search dropdown)
    public function renderResults(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'html' => ''
            ]);
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

        return response()->json([
            'success' => true,
            'html' => $result['html'],
            'count' => $result['count']
        ]);
    }

    // Full search results page
    public function index(Request $request): View
    {
        $query = trim($request->get('q', ''));
        $category = $request->get('category');
        $sort = $request->get('sort', 'relevance');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $productsQuery = Product::active()
            ->withFirstImage()
            ->with(['category:id,name,slug'])
            ->selectMinimal();

        if ($query) {
            $productsQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "{$query}%")
                  ->orWhere('name', 'like', "% {$query}%")
                  ->orWhere('slug', 'like', "{$query}%");
                
                if (strlen($query) > 3) {
                    $q->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('short_description', 'like', "%{$query}%");
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

        switch ($sort) {
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
                if ($query) {
                    $productsQuery->orderByRaw("
                        CASE 
                            WHEN name LIKE ? THEN 1
                            WHEN name LIKE ? THEN 2
                            WHEN slug LIKE ? THEN 3
                            ELSE 4
                        END
                    ", ["{$query}%", "% {$query}%", "{$query}%"]);
                }
                $productsQuery->orderBy('name', 'asc');
                break;
        }

        $products = $productsQuery->paginate(20)->withQueryString();

        $categories = \App\Models\Category::active()
            ->orderBy('name')
            ->get();

        return view('frontend.search.index', compact('products', 'query', 'category', 'sort', 'minPrice', 'maxPrice', 'categories'));
    }
}

