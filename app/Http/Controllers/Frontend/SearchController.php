<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Autocomplete search (AJAX)
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));
        
        if (strlen($query) < 2) {
            return response()->json(['products' => []]);
        }

        // Fast search with indexes - prefix matching for better performance
        $products = Product::with(['category', 'images'])
            ->active()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "{$query}%")
                  ->orWhere('name', 'like', "% {$query}%")
                  ->orWhere('slug', 'like', "{$query}%");
            })
            ->select('id', 'name', 'slug', 'total_price', 'discount_price', 'category_id')
            ->limit(8)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->total_price,
                    'discount_price' => $product->discount_price,
                    'image' => $product->main_image,
                    'category' => $product->category->name ?? '',
                    'url' => route('product.detail', $product->slug)
                ];
            });

        return response()->json(['products' => $products]);
    }

    /**
     * Full search results page
     */
    public function index(Request $request): View
    {
        $query = trim($request->get('q', ''));
        $category = $request->get('category');
        $sort = $request->get('sort', 'relevance');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $productsQuery = Product::with(['category', 'images'])
            ->active();

        // Search query
        if ($query) {
            $productsQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "{$query}%")
                  ->orWhere('name', 'like', "% {$query}%")
                  ->orWhere('slug', 'like', "{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%");
            });
        }

        // Category filter
        if ($category) {
            $productsQuery->whereHas('category', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        // Price range filter
        if ($minPrice !== null) {
            $productsQuery->where('total_price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $productsQuery->where('total_price', '<=', $maxPrice);
        }

        // Sorting
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
            default: // relevance
                if ($query) {
                    // Prioritize exact matches and prefix matches
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

        // Get categories for filter
        $categories = \App\Models\Category::active()
            ->orderBy('name')
            ->get();

        return view('frontend.search.index', compact('products', 'query', 'category', 'sort', 'minPrice', 'maxPrice', 'categories'));
    }
}

