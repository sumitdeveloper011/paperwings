<?php

namespace App\Http\Controllers\Admin\AboutSection;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AboutSectionController extends Controller
{
    /**
     * Show the form for editing the About Section.
     * Auto-creates a single record if it doesn't exist.
     */
    public function edit(): View
    {
        // Get or create the single About Section record
        $aboutSection = AboutSection::first();

        if (!$aboutSection) {
            // Auto-create if doesn't exist
            $aboutSection = AboutSection::create([
                'badge' => null,
                'title' => 'About Us',
                'description' => null,
                'button_text' => 'Learn More',
                'button_link' => '/about-us',
                'image' => null,
                'status' => 1,
            ]);
        }

        $categories = \App\Models\Category::active()->ordered()->get();
        $products = \App\Models\Product::active()->orderBy('name')->get();
        $bundles = \App\Models\ProductBundle::active()->orderBy('name')->get();

        return view('admin.about-section.edit', compact('aboutSection', 'categories', 'products', 'bundles'));
    }

    /**
     * Update the About Section.
     */
    public function update(Request $request): RedirectResponse
    {
        // Get the single About Section record
        $aboutSection = AboutSection::first();

        if (!$aboutSection) {
            // Auto-create if doesn't exist
            $aboutSection = AboutSection::create([
                'badge' => null,
                'title' => 'About Us',
                'description' => null,
                'button_text' => 'Learn More',
                'button_link' => '/about-us',
                'image' => null,
                'status' => 1,
            ]);
        }

        $validated = $request->validate([
            'badge' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|integer|in:0,1',
        ]);

        // Use final URL from smart link selector
        $buttonLink = $request->input('button_link') ?: '';
        if ($buttonLink) {
            $validated['button_link'] = $buttonLink;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($aboutSection->image && Storage::disk('public')->exists($aboutSection->image)) {
                Storage::disk('public')->delete($aboutSection->image);
            }

            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('about-sections', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        $aboutSection->update($validated);

        return redirect()->route('admin.about-sections.edit')
            ->with('success', 'About Section updated successfully!');
    }
}
