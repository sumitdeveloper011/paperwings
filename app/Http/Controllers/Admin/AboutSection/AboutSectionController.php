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
    // Display a listing of the resource
    public function index(Request $request): View
    {
        $search = $request->get('search');

        if ($search) {
            $aboutSections = AboutSection::where('title', 'like', "%{$search}%")
                ->orWhere('badge', 'like', "%{$search}%")
                ->orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $aboutSections = AboutSection::orderBy('sort_order')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('admin.about-section.index', compact('aboutSections', 'search'));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        return view('admin.about-section.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'badge' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|integer|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('about-sections', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        AboutSection::create($validated);

        return redirect()->route('admin.about-sections.index')
            ->with('success', 'About Section created successfully!');
    }

    // Display the specified resource
    public function show(AboutSection $aboutSection): View
    {
        return view('admin.about-section.show', compact('aboutSection'));
    }

    // Show the form for editing the specified resource
    public function edit(AboutSection $aboutSection): View
    {
        return view('admin.about-section.edit', compact('aboutSection'));
    }

    // Update the specified resource in storage
    public function update(Request $request, AboutSection $aboutSection): RedirectResponse
    {
        $validated = $request->validate([
            'badge' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|integer|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

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

        return redirect()->route('admin.about-sections.index')
            ->with('success', 'About Section updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(AboutSection $aboutSection): RedirectResponse
    {
        // Delete image if exists
        if ($aboutSection->image && Storage::disk('public')->exists($aboutSection->image)) {
            Storage::disk('public')->delete($aboutSection->image);
        }

        $aboutSection->delete();

        return redirect()->route('admin.about-sections.index')
            ->with('success', 'About Section deleted successfully!');
    }

    // Update status
    public function updateStatus(Request $request, AboutSection $aboutSection): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:1,0'
        ]);

        $aboutSection->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'About Section status updated successfully!');
    }
}
