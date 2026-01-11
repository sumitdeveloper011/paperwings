<?php

namespace App\Http\Controllers\Admin\Page;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageController extends Controller
{
    // Display a listing of the resource
    public function index(Request $request): \Illuminate\Contracts\View\View|JsonResponse
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
            if ($pages instanceof \Illuminate\Pagination\LengthAwarePaginator) {
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
            ]);
        }

        return view('admin.page.index', compact('pages', 'search'));
    }

    // Show the form for creating a new resource
    public function create(): \Illuminate\Contracts\View\View
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

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('pages', $imageName, 'public');
            $validated['image'] = $imagePath;
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
    public function show(Page $page): \Illuminate\Contracts\View\View
    {
        return view('admin.page.show', compact('page'));
    }

    // Show the form for editing the specified resource
    public function edit(Page $page): \Illuminate\Contracts\View\View
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

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($page->image && Storage::disk('public')->exists($page->image)) {
                Storage::disk('public')->delete($page->image);
            }

            $image = $request->file('image');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('pages', $imageName, 'public');
            $validated['image'] = $imagePath;
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
        // Delete image if exists
        if ($page->image && Storage::disk('public')->exists($page->image)) {
            Storage::disk('public')->delete($page->image);
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
            $image = $request->file('upload');
            $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('pages/content', $imageName, 'public');
            $imageUrl = asset('storage/' . $imagePath);

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
