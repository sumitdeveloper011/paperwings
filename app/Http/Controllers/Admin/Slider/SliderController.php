<?php

namespace App\Http\Controllers\Admin\Slider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\StoreSliderRequest;
use App\Http\Requests\Admin\Slider\UpdateSliderRequest;
use App\Models\Slider;
use App\Repositories\Interfaces\SliderRepositoryInterface;
use App\Services\ImageService;
use App\Traits\LoadsFormData;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SliderController extends Controller
{
    use LoadsFormData;
    protected SliderRepositoryInterface $sliderRepository;
    protected ImageService $imageService;

    public function __construct(SliderRepositoryInterface $sliderRepository, ImageService $imageService)
    {
        $this->sliderRepository = $sliderRepository;
        $this->imageService = $imageService;
    }

    public function index(): View
    {
        $sliders = $this->sliderRepository->getOrdered();

        return view('admin.slider.index', compact('sliders'));
    }

    // Show the form for creating a new resource
    public function create(): View
    {
        $formData = $this->getFormData();

        return view('admin.slider.create', $formData);
    }

    public function store(StoreSliderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Generate UUID first (will be used for folder name)
        $sliderUuid = Str::uuid()->toString();
        $validated['uuid'] = $sliderUuid;

        // Upload image with slider UUID using ImageService
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->uploadImage($request->file('image'), 'sliders', $sliderUuid);
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
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

    public function show(Slider $slider): View
    {
        return view('admin.slider.show', compact('slider'));
    }

    public function edit(Slider $slider): View
    {
        $formData = $this->getFormData();

        return view('admin.slider.edit', array_merge(['slider' => $slider], $formData));
    }

    public function update(UpdateSliderRequest $request, Slider $slider): RedirectResponse
    {
        $validated = $request->validated();

        // Use final URL values from smart link selector
        $button1Url = $request->input('button_1_url') ?: '';
        $button2Url = $request->input('button_2_url') ?: '';

        // Update image using ImageService
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->updateImage(
                $request->file('image'),
                'sliders',
                $slider->uuid,
                $slider->image
            );
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
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

    public function destroy(Slider $slider): RedirectResponse
    {
        // Delete image using ImageService
        if ($slider->image) {
            $this->imageService->deleteImage($slider->image);
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
    public function moveUp(Request $request, Slider $slider): RedirectResponse|JsonResponse
    {
        $result = $this->sliderRepository->moveUp($slider);

        if ($request->ajax() || $request->expectsJson()) {
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Slider moved up successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Slider is already at the top position.',
                    'type' => 'info'
                ], 200);
            }
        }

        if ($result) {
            return redirect()->back()
                            ->with('success', 'Slider moved up successfully!');
        }

        return redirect()->back()
                        ->with('error', 'Failed to move slider up. No previous slider found.');
    }

    // Move slider down in order
    public function moveDown(Request $request, Slider $slider): RedirectResponse|JsonResponse
    {
        $result = $this->sliderRepository->moveDown($slider);

        if ($request->ajax() || $request->expectsJson()) {
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Slider moved down successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Slider is already at the bottom position.',
                    'type' => 'info'
                ], 200);
            }
        }

        if ($result) {
            return redirect()->back()
                            ->with('success', 'Slider moved down successfully!');
        }

        return redirect()->back()
                        ->with('error', 'Failed to move slider down. No next slider found.');
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
