<?php

namespace App\Http\Controllers\Admin\Slider;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Repositories\Interfaces\SliderRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $sliders = $this->sliderRepository->getOrdered();
        
        return view('admin.slider.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.slider.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('sliders', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Handle buttons
        $buttons = [];
        if ($validated['button_1_name'] && $validated['button_1_url']) {
            $buttons[] = [
                'name' => $validated['button_1_name'],
                'url' => $validated['button_1_url']
            ];
        }
        if ($validated['button_2_name'] && $validated['button_2_url']) {
            $buttons[] = [
                'name' => $validated['button_2_name'],
                'url' => $validated['button_2_url']
            ];
        }
        $validated['buttons'] = !empty($buttons) ? $buttons : null;

        // Remove button fields from validated data as they're now in buttons array
        unset($validated['button_1_name'], $validated['button_1_url'], $validated['button_2_name'], $validated['button_2_url']);

        $this->sliderRepository->create($validated);

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider): View
    {
        return view('admin.slider.show', compact('slider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider): View
    {
        return view('admin.slider.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'heading' => 'required|string|max:255',
            'sub_heading' => 'nullable|string|max:255',
            'button_1_name' => 'nullable|string|max:100',
            'button_1_url' => 'nullable|url|max:255',
            'button_2_name' => 'nullable|string|max:100',
            'button_2_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:1',
            'status' => 'required|in:1,0'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }

            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('sliders', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Handle buttons
        $buttons = [];
        if ($validated['button_1_name'] && $validated['button_1_url']) {
            $buttons[] = [
                'name' => $validated['button_1_name'],
                'url' => $validated['button_1_url']
            ];
        }
        if ($validated['button_2_name'] && $validated['button_2_url']) {
            $buttons[] = [
                'name' => $validated['button_2_name'],
                'url' => $validated['button_2_url']
            ];
        }
        $validated['buttons'] = !empty($buttons) ? $buttons : null;

        // Remove button fields from validated data
        unset($validated['button_1_name'], $validated['button_1_url'], $validated['button_2_name'], $validated['button_2_url']);

        $this->sliderRepository->update($slider, $validated);

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider): RedirectResponse
    {
        // Delete image if it exists
        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }

        $this->sliderRepository->delete($slider);

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider deleted successfully!');
    }

    /**
     * Update slider status
     */
    public function updateStatus(Request $request, Slider $slider): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:1,0'
        ]);

        $this->sliderRepository->updateStatus($slider, $validated['status']);

        return redirect()->back()
                        ->with('success', 'Slider status updated successfully!');
    }

    /**
     * Move slider up in order
     */
    public function moveUp(Slider $slider): RedirectResponse
    {
        $this->sliderRepository->moveUp($slider);

        return redirect()->back()
                        ->with('success', 'Slider moved up successfully!');
    }

    /**
     * Move slider down in order
     */
    public function moveDown(Slider $slider): RedirectResponse
    {
        $this->sliderRepository->moveDown($slider);

        return redirect()->back()
                        ->with('success', 'Slider moved down successfully!');
    }

    /**
     * Update slider sort order via AJAX
     */
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

    /**
     * Duplicate slider
     */
    public function duplicate(Slider $slider): RedirectResponse
    {
        $sliderData = $slider->toArray();
        
        // Remove unique fields
        unset($sliderData['id'], $sliderData['uuid'], $sliderData['created_at'], $sliderData['updated_at']);
        
        // Update heading to indicate it's a copy
        $sliderData['heading'] = $sliderData['heading'] . ' (Copy)';
        
        // Set new sort order
        $sliderData['sort_order'] = $this->sliderRepository->getNextSortOrder();
        
        // Copy image if it exists
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