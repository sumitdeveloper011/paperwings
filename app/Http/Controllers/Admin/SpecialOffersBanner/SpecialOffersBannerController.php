<?php

namespace App\Http\Controllers\Admin\SpecialOffersBanner;

use App\Http\Controllers\Controller;
use App\Models\SpecialOffersBanner;
use App\Http\Requests\Admin\SpecialOffersBanner\StoreSpecialOffersBannerRequest;
use App\Http\Requests\Admin\SpecialOffersBanner\UpdateSpecialOffersBannerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SpecialOffersBannerController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');

        $query = SpecialOffersBanner::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $banners = $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            // Only show pagination if there are banners and multiple pages
            if ($banners->total() > 0 && $banners->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $banners
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.special-offers-banner.partials.table', compact('banners'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.special-offers-banner.index', compact('banners', 'search'));
    }

    public function create(): View
    {
        $categories = \App\Models\Category::active()->ordered()->get();
        $products = \App\Models\Product::active()->orderBy('name')->get();
        $bundles = \App\Models\ProductBundle::active()->orderBy('name')->get();

        return view('admin.special-offers-banner.create', compact('categories', 'products', 'bundles'));
    }

    public function store(StoreSpecialOffersBannerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('banners', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Extract final URL from smart-link-selector
        $validated['button_link'] = $request->input('button_link') ?: null;

        SpecialOffersBanner::create($validated);

        return redirect()->route('admin.special-offers-banners.index')
            ->with('success', 'Special Offers Banner created successfully!');
    }

    public function show(SpecialOffersBanner $specialOffersBanner): View
    {
        return view('admin.special-offers-banner.show', compact('specialOffersBanner'));
    }

    public function edit(SpecialOffersBanner $specialOffersBanner): View
    {
        $categories = \App\Models\Category::active()->ordered()->get();
        $products = \App\Models\Product::active()->orderBy('name')->get();
        $bundles = \App\Models\ProductBundle::active()->orderBy('name')->get();

        return view('admin.special-offers-banner.edit', compact('specialOffersBanner', 'categories', 'products', 'bundles'));
    }

    public function update(UpdateSpecialOffersBannerRequest $request, SpecialOffersBanner $specialOffersBanner): RedirectResponse
    {
        $validated = $request->validated();

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image == '1') {
            // Delete old image
            if ($specialOffersBanner->image && Storage::disk('public')->exists($specialOffersBanner->image)) {
                Storage::disk('public')->delete($specialOffersBanner->image);
            }
            $validated['image'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($specialOffersBanner->image && Storage::disk('public')->exists($specialOffersBanner->image)) {
                Storage::disk('public')->delete($specialOffersBanner->image);
            }

            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('banners', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Extract final URL from smart-link-selector
        $validated['button_link'] = $request->input('button_link') ?: null;

        $specialOffersBanner->update($validated);

        return redirect()->route('admin.special-offers-banners.index')
            ->with('success', 'Special Offers Banner updated successfully!');
    }

    public function destroy(SpecialOffersBanner $specialOffersBanner): RedirectResponse
    {
        // Delete image if exists
        if ($specialOffersBanner->image && Storage::disk('public')->exists($specialOffersBanner->image)) {
            Storage::disk('public')->delete($specialOffersBanner->image);
        }

        $specialOffersBanner->delete();

        return redirect()->route('admin.special-offers-banners.index')
            ->with('success', 'Special Offers Banner deleted successfully!');
    }

    public function updateStatus(Request $request, SpecialOffersBanner $specialOffersBanner): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1'
        ]);

        // Cast status to integer
        $status = (int) $validated['status'];
        $specialOffersBanner->update(['status' => $status]);

        $statusText = $status == 1 ? 'activated' : 'deactivated';

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Banner {$statusText} successfully!"
            ]);
        }

        return redirect()->back()
            ->with('success', "Banner {$statusText} successfully!");
    }
}
