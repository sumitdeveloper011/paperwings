<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    // Display a listing of the resource
    public function index(Request $request): ViewContract|JsonResponse
    {
        $search = $request->get('search', '');

        // Build query
        $query = Page::query();

        // Apply search filter
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('sub_title', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // Get paginated results
        $pages = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withPath($request->url())
            ->appends($request->except('page'));

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($pages instanceof LengthAwarePaginator) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $pages
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.page.partials.table', compact('pages'))->render(),
                'pagination' => $paginationHtml,
                'total' => $pages->total(),
            ]);
        }

        return view('admin.page.index', compact('pages', 'search'));
    }

    // Show the form for creating a new resource
    public function create(): ViewContract
    {
        return view('admin.page.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:pages,title',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'sub_title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|boolean',
        ]);

        // Generate UUID first (will be used for folder name)
        $pageUuid = Str::uuid()->toString();
        $validated['uuid'] = $pageUuid;

        // Upload image with page UUID using ImageService
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->uploadImage($request->file('image'), 'pages', $pageUuid);
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = 1;
        }

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully!');
    }

    // Display the specified resource
    public function show(Page $page): ViewContract
    {
        return view('admin.page.show', compact('page'));
    }

    // Show the form for editing the specified resource
    public function edit(Page $page): ViewContract
    {
        return view('admin.page.edit', compact('page'));
    }

    // Update the specified resource in storage
    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:pages,title,' . $page->id,
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'sub_title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|boolean',
        ]);

        // Update image using ImageService
        if ($request->hasFile('image')) {
            $imagePath = $this->imageService->updateImage(
                $request->file('image'),
                'pages',
                $page->uuid,
                $page->image
            );
            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully!');
    }

    // Remove the specified resource from storage
    public function destroy(Page $page): RedirectResponse
    {
        // Delete image using ImageService
        if ($page->image) {
            $this->imageService->deleteImage($page->image);
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully!');
    }

    // Upload image from CKEditor
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('upload')) {
            $imagePath = $this->imageService->uploadSimple(
                $request->file('upload'),
                'pages/content'
            );
            $imageUrl = $imagePath ? asset('storage/' . $imagePath) : null;

            return response()->json([
                'url' => $imageUrl
            ]);
        }

        return response()->json(['error' => ['message' => 'Image upload failed']], 400);
    }

    // Update page status
    public function updateStatus(Request $request, Page $page): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:1,0'
        ]);

        $page->update(['status' => $validated['status']]);

        // Handle AJAX requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Page status updated successfully!'
            ]);
        }

        return redirect()->back()
                        ->with('success', 'Page status updated successfully!');
    }
}
