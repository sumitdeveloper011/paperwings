<?php

namespace App\Http\Controllers\Admin\ProductFaq;

use App\Http\Controllers\Controller;
use App\Models\ProductFaq;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductFaqController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $productId = $request->get('product_id');

        $query = ProductFaq::with('product');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        $faqs = $query->ordered()->paginate(15);
        $products = Product::active()->orderBy('name')->get();

        return view('admin.product-faq.index', compact('faqs', 'search', 'productId', 'products'));
    }

    public function create(): View
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.product-faq.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        ProductFaq::create($validated);

        return redirect()->route('admin.product-faqs.index')
            ->with('success', 'Product FAQ created successfully!');
    }

    public function show(ProductFaq $productFaq): View
    {
        $productFaq->load('product');
        return view('admin.product-faq.show', compact('productFaq'));
    }

    public function edit(ProductFaq $productFaq): View
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.product-faq.edit', compact('productFaq', 'products'));
    }

    public function update(Request $request, ProductFaq $productFaq): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ]);

        $productFaq->update($validated);

        return redirect()->route('admin.product-faqs.index')
            ->with('success', 'Product FAQ updated successfully!');
    }

    public function destroy(ProductFaq $productFaq): RedirectResponse
    {
        $productFaq->delete();
        return redirect()->route('admin.product-faqs.index')
            ->with('success', 'Product FAQ deleted successfully!');
    }

    public function updateStatus(Request $request, ProductFaq $productFaq): RedirectResponse
    {
        $request->validate(['status' => 'required|in:1,0']);
        $productFaq->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Product FAQ status updated successfully!');
    }
}
