<?php

namespace App\Http\Controllers\Admin\SpecialOffersBanner;

use App\Http\Controllers\Controller;
use App\Models\SpecialOffersBanner;
use App\Http\Requests\Admin\SpecialOffersBanner\StoreSpecialOffersBannerRequest;
use App\Http\Requests\Admin\SpecialOffersBanner\UpdateSpecialOffersBannerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SpecialOffersBannerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');

        if ($search) {
            $banners = SpecialOffersBanner::where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $banners = SpecialOffersBanner::orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('admin.special-offers-banner.index', compact('banners', 'search'));
    }

    public function create(): View
    {
        return view('admin.special-offers-banner.create');
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
        return view('admin.special-offers-banner.edit', compact('specialOffersBanner'));
    }

    public function update(UpdateSpecialOffersBannerRequest $request, SpecialOffersBanner $specialOffersBanner): RedirectResponse
    {
        $validated = $request->validated();

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

    public function updateStatus(Request $request, SpecialOffersBanner $specialOffersBanner): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:1,0'
        ]);

        $specialOffersBanner->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Banner status updated successfully!');
    }
}
