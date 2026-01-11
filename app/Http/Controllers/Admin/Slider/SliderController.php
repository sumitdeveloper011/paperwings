<?php

namespace App\Http\Controllers\Admin\Slider;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Repositories\Interfaces\SliderRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    protected SliderRepositoryInterface $sliderRepository;

    public function __construct(SliderRepositoryInterface $sliderRepository)
    {
        $this->sliderRepository = $sliderRepository;
    }

    // Display a listing of the resource
    public function index(): View
    {
        $sliders = $this->sliderRepository->getOrdered();

        return view('admin.slider.index', compact('sliders'));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        $categories = \App\Models\Category::active()->ordered()->get();
        $products = \App\Models\Product::active()->orderBy('name')->get();
        $bundles = \App\Models\ProductBundle::active()->orderBy('name')->get();

        return view('admin.slider.create', compact('categories', 'products', 'bundles'));
    }

    // Store a newly created resource in storage
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'heading' => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
            'button_1_name' => 'nullable|string|max:100',
            'button_1_url' => 'nullable|url|max:255',
            'button_2_name' => 'nullable|string|max:100',
            'button_2_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:1',
            'status' => 'required|in:1,0'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('sliders', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Handle buttons - use final URL values from smart link selector
        $buttons = [];
        $button1Url = $request->input('button_1_url') ?: '';
        $button2Url = $request->input('button_2_url') ?: '';

        if ($validated['button_1_name'] && $button1Url) {
            $buttons[] = [
                'name' => $validated['button_1_name'],
                'url' => $button1Url
            ];
        }
        if ($validated['button_2_name'] && $button2Url) {
            $buttons[] = [
                'name' => $validated['button_2_name'],
                'url' => $button2Url
            ];
        }
        $validated['buttons'] = !empty($buttons) ? $buttons : null;

        unset($validated['button_1_name'], $validated['button_1_url'], $validated['button_2_name'], $validated['button_2_url']);

        $this->sliderRepository->create($validated);

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider created successfully!');
    }

    // Display the specified resource
    public function show(Slider $slider): View
    {
        return view('admin.slider.show', compact('slider'));
    }

    // Show the form for editing the specified resource
    public function edit(Slider $slider): View
    {
        $categories = \App\Models\Category::active()->ordered()->get();
        $products = \App\Models\Product::active()->orderBy('name')->get();
        $bundles = \App\Models\ProductBundle::active()->orderBy('name')->get();

        return view('admin.slider.edit', compact('slider', 'categories', 'products', 'bundles'));
    }

    // Update the specified resource in storage
    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'heading' => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
            'button_1_name' => 'nullable|string|max:100',
            'button_1_url' => 'nullable|string|max:500',
            'button_2_name' => 'nullable|string|max:100',
            'button_2_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:1',
            'status' => 'required|in:1,0'
        ]);

        // Use final URL values from smart link selector
        $button1Url = $request->input('button_1_url') ?: '';
        $button2Url = $request->input('button_2_url') ?: '';

        if ($request->hasFile('image')) {
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('sliders', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Handle buttons - use final URL values from smart link selector
        $buttons = [];
        if ($validated['button_1_name'] && $button1Url) {
            $buttons[] = [
                'name' => $validated['button_1_name'],
                'url' => $button1Url
            ];
        }
        if ($validated['button_2_name'] && $button2Url) {
            $buttons[] = [
                'name' => $validated['button_2_name'],
                'url' => $button2Url
            ];
        }
        $validated['buttons'] = !empty($buttons) ? $buttons : null;

        unset($validated['button_1_name'], $validated['button_1_url'], $validated['button_2_name'], $validated['button_2_url']);

        $this->sliderRepository->update($slider, $validated);

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(Slider $slider): RedirectResponse
    {
        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }

        $this->sliderRepository->delete($slider);

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider deleted successfully!');
    }

    // Update slider status
    public function updateStatus(Request $request, Slider $slider): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:1,0'
        ]);

        $this->sliderRepository->updateStatus($slider, $validated['status']);

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Slider status updated successfully!'
            ]);
        }

        return redirect()->back()
                        ->with('success', 'Slider status updated successfully!');
    }

    // Move slider up in order
    public function moveUp(Slider $slider): RedirectResponse
    {
        $this->sliderRepository->moveUp($slider);

        return redirect()->back()
                        ->with('success', 'Slider moved up successfully!');
    }

    // Move slider down in order
    public function moveDown(Slider $slider): RedirectResponse
    {
        $this->sliderRepository->moveDown($slider);

        return redirect()->back()
                        ->with('success', 'Slider moved down successfully!');
    }

    // Update slider sort order via AJAX
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'sliders' => 'required|array',
            'sliders.*' => 'required|integer|exists:sliders,id'
        ]);

        $success = $this->sliderRepository->reorderSliders($validated['sliders']);

        if ($success) {
            return response()->json(['success' => true, 'message' => 'Slider order updated successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to update slider order.'], 500);
    }

    // Duplicate slider
    public function duplicate(Slider $slider): RedirectResponse
    {
        $sliderData = $slider->toArray();

        unset($sliderData['id'], $sliderData['uuid'], $sliderData['created_at'], $sliderData['updated_at']);

        $sliderData['heading'] = $sliderData['heading'] . ' (Copy)';

        $sliderData['sort_order'] = $this->sliderRepository->getNextSortOrder();

        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            $originalPath = $slider->image;
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $newImageName = Str::uuid() . '.' . $extension;
            $newImagePath = 'sliders/' . $newImageName;

            Storage::disk('public')->copy($originalPath, $newImagePath);
            $sliderData['image'] = $newImagePath;
        }

        $this->sliderRepository->create($sliderData);

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider duplicated successfully!');
    }
}
