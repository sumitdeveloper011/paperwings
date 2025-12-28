<?php

namespace App\Http\Controllers\Admin\Bundle;

use App\Http\Controllers\Controller;
use App\Models\ProductBundle;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = ProductBundle::withCount('products');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        $bundles = $query->ordered()->paginate(15);

        return view('admin.bundle.index', compact('bundles', 'search', 'status'));
    }

    public function create(): View
    {
        $categories = \App\Models\Category::active()->ordered()->get();
        return view('admin.bundle.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bundle_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'product_ids' => 'required|array|min:2',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'nullable|array',
            'quantities.*' => 'integer|min:1',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('bundles', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        $bundle = ProductBundle::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image' => $validated['image'] ?? null,
            'bundle_price' => $validated['bundle_price'],
            'discount_percentage' => $validated['discount_percentage'] ?? null,
            'status' => $validated['status'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        // Attach products
        foreach ($validated['product_ids'] as $index => $productId) {
            $bundle->products()->attach($productId, [
                'quantity' => $validated['quantities'][$index] ?? 1
            ]);
        }

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle created successfully!');
    }

    public function show(ProductBundle $bundle): View
    {
        $bundle->load('products.images');
        return view('admin.bundle.show', compact('bundle'));
    }

    public function edit(ProductBundle $bundle): View
    {
        $bundle->load('products');
        $categories = \App\Models\Category::active()->ordered()->get();
        return view('admin.bundle.edit', compact('bundle', 'categories'));
    }

    public function searchProducts(Request $request)
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
                  ->orWhere('sku', 'like', "%{$search}%");
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
                'image' => $product->main_image,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $products->count() === $perPage
            ]
        ]);
    }

    public function update(Request $request, ProductBundle $bundle): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bundle_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'product_ids' => 'required|array|min:2',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'nullable|array',
            'quantities.*' => 'integer|min:1',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($bundle->image && Storage::disk('public')->exists($bundle->image)) {
                Storage::disk('public')->delete($bundle->image);
            }
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('bundles', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        $bundle->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image' => $validated['image'] ?? $bundle->image,
            'bundle_price' => $validated['bundle_price'],
            'discount_percentage' => $validated['discount_percentage'] ?? null,
            'status' => $validated['status'] ?? $bundle->status,
            'sort_order' => $validated['sort_order'] ?? $bundle->sort_order,
        ]);

        // Sync products
        $bundle->products()->detach();
        foreach ($validated['product_ids'] as $index => $productId) {
            $bundle->products()->attach($productId, [
                'quantity' => $validated['quantities'][$index] ?? 1
            ]);
        }

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle updated successfully!');
    }

    public function destroy(ProductBundle $bundle): RedirectResponse
    {
        if ($bundle->image && Storage::disk('public')->exists($bundle->image)) {
            Storage::disk('public')->delete($bundle->image);
        }
        $bundle->delete();
        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle deleted successfully!');
    }

    public function updateStatus(Request $request, ProductBundle $bundle): RedirectResponse
    {
        $request->validate(['status' => 'required|in:1,0']);
        $bundle->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Bundle status updated successfully!');
    }
}
