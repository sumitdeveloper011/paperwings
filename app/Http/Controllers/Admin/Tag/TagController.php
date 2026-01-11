<?php

namespace App\Http\Controllers\Admin\Tag;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tag\StoreTagRequest;
use App\Http\Requests\Admin\Tag\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $query = Tag::withCount('products');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $tags = $query->orderBy('name')->paginate(15);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            return response()->json([
                'success' => true,
                'html' => view('admin.tag.partials.table', compact('tags'))->render(),
                'pagination' => $tags->total() > 0 && $tags->hasPages()
                    ? view('components.pagination', ['paginator' => $tags])->render()
                    : ''
            ]);
        }

        return view('admin.tag.index', compact('tags', 'search'));
    }

    public function create(): View
    {
        return view('admin.tag.create');
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Tag::create($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created successfully!');
    }

    public function show(Tag $tag): View
    {
        $tag->load(['products' => function($query) {
            $query->with('images')->limit(20);
        }]);
        return view('admin.tag.show', compact('tag'));
    }

    public function edit(Tag $tag): View
    {
        return view('admin.tag.edit', compact('tag'));
    }

    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validated();

        $tag->update($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag updated successfully!');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();
        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully!');
    }
}
