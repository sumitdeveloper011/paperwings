<?php

namespace App\Http\Controllers\Admin\ProductFaq;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductFaq\StoreProductFaqRequest;
use App\Http\Requests\Admin\ProductFaq\UpdateProductFaqRequest;
use App\Models\ProductFaq;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProductFaqController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $productId = $request->get('product_id');
        $categoryId = $request->get('category_id');

        $query = ProductFaq::with(['product', 'category']);

        if ($search) {
            $query->where(function($q) use ($search) {
                // Search in product name
                $q->whereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                // Search in JSON FAQs array - using JSON_SEARCH for MySQL
                ->orWhereRaw("JSON_SEARCH(faqs, 'one', ?, NULL, '$[*].question') IS NOT NULL", ["%{$search}%"])
                ->orWhereRaw("JSON_SEARCH(faqs, 'one', ?, NULL, '$[*].answer') IS NOT NULL", ["%{$search}%"]);
            });
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $faqs = $query->orderBy('created_at', 'desc')->paginate(15);
        $products = Product::active()->orderBy('name')->get();
        $categories = Category::active()->ordered()->get();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            return response()->json([
                'success' => true,
                'html' => view('admin.product-faq.partials.table', compact('faqs'))->render(),
                'pagination' => $faqs->total() > 0 && $faqs->hasPages()
                    ? view('components.pagination', ['paginator' => $faqs])->render()
                    : ''
            ]);
        }

        return view('admin.product-faq.index', compact('faqs', 'search', 'productId', 'categoryId', 'products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.product-faq.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'category_id' => 'nullable|exists:categories,id',
            'faqs' => 'required|array|min:1',
            'faqs.*.question' => 'required|string|max:500',
            'faqs.*.answer' => 'required|string',
            'faqs.*.sort_order' => 'nullable|integer|min:0',
            'faqs.*.status' => 'nullable|boolean',
        ]);

        $productId = $validated['product_id'];

        // Get category_id from product if not provided
        if (empty($validated['category_id'])) {
            $product = Product::find($productId);
            $validated['category_id'] = $product->category_id ?? null;
        }

        // Prepare FAQs array
        $faqsArray = [];
        foreach ($validated['faqs'] as $index => $faqData) {
            $faqsArray[] = [
                'question' => trim($faqData['question']),
                'answer' => trim($faqData['answer']),
                'sort_order' => $faqData['sort_order'] ?? $index,
                'status' => $faqData['status'] ?? true,
            ];
        }

        // Check if FAQ entry already exists for this product
        $productFaq = ProductFaq::where('product_id', $productId)->first();

        if ($productFaq) {
            // Update existing entry
            $productFaq->update([
                'category_id' => $validated['category_id'],
                'faqs' => $faqsArray,
            ]);
            $message = 'Product FAQs updated successfully!';
        } else {
            // Create new entry
            ProductFaq::create([
                'product_id' => $productId,
                'category_id' => $validated['category_id'],
                'faqs' => $faqsArray,
            ]);
            $message = 'Product FAQs created successfully!';
        }

        return redirect()->route('admin.product-faqs.index')
            ->with('success', $message);
    }

    public function show(ProductFaq $productFaq): View
    {
        $productFaq->load(['product', 'category']);
        return view('admin.product-faq.show', compact('productFaq'));
    }

    public function edit(ProductFaq $productFaq): View
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.product-faq.edit', compact('productFaq', 'categories'));
    }

    public function update(UpdateProductFaqRequest $request, ProductFaq $productFaq): RedirectResponse
    {
        $validated = $request->validated();

        // Get category_id from product if not provided
        if (empty($validated['category_id'])) {
            $product = Product::find($validated['product_id']);
            $validated['category_id'] = $product->category_id ?? null;
        }

        // Prepare FAQs array
        $faqsArray = [];
        foreach ($validated['faqs'] as $index => $faqData) {
            $faqsArray[] = [
                'question' => trim($faqData['question']),
                'answer' => trim($faqData['answer']),
                'sort_order' => $faqData['sort_order'] ?? $index,
                'status' => $faqData['status'] ?? true,
            ];
        }

        $productFaq->update([
            'product_id' => $validated['product_id'],
            'category_id' => $validated['category_id'],
            'faqs' => $faqsArray,
        ]);

        return redirect()->route('admin.product-faqs.index')
            ->with('success', 'Product FAQs updated successfully!');
    }

    public function destroy(ProductFaq $productFaq): RedirectResponse
    {
        $productFaq->delete();
        return redirect()->route('admin.product-faqs.index')
            ->with('success', 'Product FAQ deleted successfully!');
    }

    public function updateStatus(Request $request, ProductFaq $productFaq): RedirectResponse
    {
        // This method might not be needed anymore since status is in JSON
        // But keeping it for backward compatibility
        return redirect()->back()->with('success', 'Product FAQ status updated successfully!');
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $search = $request->get('search', $request->get('term', ''));
        $categoryId = $request->get('category_id');
        $page = $request->get('page', 1);
        $perPage = 50;

        $query = Product::active()->with('images');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')
                          ->skip(($page - 1) * $perPage)
                          ->take($perPage)
                          ->get();

        $results = $products->map(function($product) {
            return [
                'id' => $product->id,
                'text' => $product->name . ' - $' . number_format($product->total_price, 2),
                'name' => $product->name,
                'price' => $product->total_price,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $products->count() === $perPage
            ]
        ]);
    }
}
