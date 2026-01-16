<?php

namespace App\Http\Controllers\Admin\Testimonial;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Http\Requests\Admin\Testimonial\StoreTestimonialRequest;
use App\Http\Requests\Admin\Testimonial\UpdateTestimonialRequest;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');

        $query = Testimonial::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('review', 'like', "%{$search}%");
            });
        }

        $testimonials = $query->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            // Only show pagination if there are testimonials and multiple pages
            if ($testimonials->total() > 0 && $testimonials->hasPages()) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $testimonials
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.testimonial.partials.table', compact('testimonials'))->render(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('admin.testimonial.index', compact('testimonials', 'search'));
    }

    public function create(): View
    {
        return view('admin.testimonial.create');
    }

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Generate UUID first (will be used for folder name)
        $testimonialUuid = Str::uuid()->toString();
        $validated['uuid'] = $testimonialUuid;

        // Upload image with testimonial UUID using ImageService
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->uploadImage($request->file('image'), 'testimonials', $testimonialUuid);
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully!');
    }

    public function show(Testimonial $testimonial): View
    {
        return view('admin.testimonial.show', compact('testimonial'));
    }

    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonial.edit', compact('testimonial'));
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validated();

        // Handle image removal
        if ($request->has('remove_image') && $request->remove_image == '1') {
            // Delete old image using ImageService
            if ($testimonial->image) {
                $this->imageService->deleteImage($testimonial->image);
            }
            $validated['image'] = null;
        }

        // Update image using ImageService
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->updateImage(
                $request->file('image'),
                'testimonials',
                $testimonial->uuid,
                $testimonial->image
            );
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully!');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        // Delete image using ImageService
        if ($testimonial->image) {
            $this->imageService->deleteImage($testimonial->image);
        }

        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial deleted successfully!');
    }

    public function updateStatus(Request $request, Testimonial $testimonial): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1'
        ]);

        $status = (int) $validated['status'];
        $testimonial->update(['status' => $status]);

        $statusLabel = $status == 1 ? 'Active' : 'Inactive';

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Testimonial set to {$statusLabel}"
            ]);
        }

        return redirect()->back()
            ->with('success', "Testimonial set to {$statusLabel}");
    }
}
