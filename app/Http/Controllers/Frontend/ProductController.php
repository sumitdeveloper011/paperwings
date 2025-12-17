<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $viewPath = 'frontend.product.';

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

    public function productDetail($slug){
        try {
            $title = 'Product Detail';
            if (!$slug) {
                return view('frontend.errors.404', [
                    'title' => 'Product Not Found',
                    'message' => 'Product not found. The product you are looking for does not exist.'
                ]);
            }
            $product = Product::with('images')->with('accordions')->active()->where('slug', $slug)->first();
            $relatedProducts = Product::with('images')->active()->where('eposnow_category_id', $product->eposnow_category_id)->where('id', '!=', $product->id)->take(4)->get();
            if (!$product) {
                return view('frontend.errors.404', [
                    'title' => 'Product Not Found',
                    'message' => 'Product not found. The product you are looking for does not exist.'
                ]);
            }
            return view($this->viewPath . 'product-detail', compact('title', 'product', 'relatedProducts'));
        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => 'Error',
                'message' => $e->getMessage()
            ]);
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

            // Calculate min and max prices from all products in category (for filter display)
            $allProducts = Product::where('eposnow_category_id', $category->eposnow_category_id)
                ->active()
                ->get();

            $priceMin = 0;
            $priceMax = 0;

            if ($allProducts->count() > 0) {
                $prices = $allProducts->map(function($product) {
                    return $product->discount_price ?? $product->total_price;
                })->filter()->values();

                if ($prices->count() > 0) {
                    $priceMin = floor($prices->min());
                    $priceMax = ceil($prices->max());
                    // Round up to nearest 10 for better display
                    $priceMax = ceil($priceMax / 10) * 10;
                }
            }

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

            // Get categories with product count for sidebar
            $categories = Category::active()
                ->withCount(['products' => function($query) {
                    $query->where('status', 1);
                }])
                ->ordered()
                ->get();

            return view('frontend.category.category', compact('title', 'category', 'products', 'categories', 'sort', 'priceMin', 'priceMax', 'minPrice', 'maxPrice'));

        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => '404 - Error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
