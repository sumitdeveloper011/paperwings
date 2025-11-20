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

    public function productByCategory($slug){
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
            $products = Product::where('eposnow_category_id', $category->eposnow_category_id)
                ->active()
                ->orderBy('created_at', 'desc')
                ->paginate(12); // 12 products per page

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

            return view('frontend.category.category', compact('title', 'category', 'products', 'categories'));

        } catch (\Exception $e) {
            return view('frontend.errors.404', [
                'title' => '404 - Error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
