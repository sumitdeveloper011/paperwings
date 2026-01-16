<?php

namespace App\Http\Controllers\Admin\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Gallery\StoreGalleryRequest;
use App\Http\Requests\Admin\Gallery\UpdateGalleryRequest;
use App\Models\Gallery;
use App\Repositories\Interfaces\GalleryRepositoryInterface;
use App\Services\GalleryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    protected GalleryService $galleryService;
    protected GalleryRepositoryInterface $galleryRepository;

    public function __construct(
        GalleryService $galleryService,
        GalleryRepositoryInterface $galleryRepository
    ) {
        $this->galleryService = $galleryService;
        $this->galleryRepository = $galleryRepository;
    }

    public function index(Request $request): ViewContract|JsonResponse
    {
        $search = $request->get('search', '');
        $category = $request->get('category', '');
        $status = $request->get('status', '');

        if ($search !== '' || $category !== '' || $status !== '') {
            $query = Gallery::query()->with(['creator', 'coverImage']);

            if ($search !== '') {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('slug', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            if ($category !== '') {
                $query->where('category', $category);
            }

            if ($status !== '') {
                $query->where('status', $status);
            }

            $galleries = $query->withCount('items')
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withPath($request->url())
                ->appends($request->except('page'));
        } else {
            $galleries = Gallery::query()
                ->with(['creator', 'coverImage'])
                ->withCount('items')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            $galleries->setPath($request->url());
            $galleries->appends($request->except('page'));
        }

        if ($request->ajax() || $request->expectsJson() || $request->has('ajax')) {
            $paginationHtml = '';
            if ($galleries instanceof LengthAwarePaginator) {
                $paginationHtml = '<div class="pagination-wrapper">' .
                    view('components.pagination', [
                        'paginator' => $galleries
                    ])->render() .
                    '</div>';
            }

            return response()->json([
                'success' => true,
                'html' => view('admin.gallery.partials.table', compact('galleries'))->render(),
                'pagination' => $paginationHtml,
                'total' => $galleries->total(),
            ]);
        }

        $categories = [
            'general' => 'General',
            'products' => 'Products',
            'events' => 'Events',
            'portfolio' => 'Portfolio',
            'other' => 'Other'
        ];

        return view('admin.gallery.index', compact('galleries', 'search', 'category', 'status', 'categories'));
    }

    public function create(): ViewContract
    {
        $categories = [
            'general' => 'General',
            'products' => 'Products',
            'events' => 'Events',
            'portfolio' => 'Portfolio',
            'other' => 'Other'
        ];
        return view('admin.gallery.create', compact('categories'));
    }

    public function store(StoreGalleryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['created_by'] = Auth::id();

        $gallery = $this->galleryRepository->create($validated);

        return redirect()->route('admin.galleries.show', $gallery->uuid)
            ->with('success', 'Gallery created successfully!');
    }

    public function show(Gallery $gallery): ViewContract
    {
        $gallery = $this->galleryRepository->findByUuid($gallery->uuid);
        
        if (!$gallery) {
            abort(404);
        }

        $gallery->load(['items' => function($query) {
            $query->orderBy('order');
        }]);

        return view('admin.gallery.show', compact('gallery'));
    }

    public function edit(Gallery $gallery): ViewContract
    {
        $gallery = $this->galleryRepository->findByUuid($gallery->uuid);
        
        if (!$gallery) {
            abort(404);
        }

        $categories = [
            'general' => 'General',
            'products' => 'Products',
            'events' => 'Events',
            'portfolio' => 'Portfolio',
            'other' => 'Other'
        ];
        return view('admin.gallery.edit', compact('gallery', 'categories'));
    }

    public function update(UpdateGalleryRequest $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validated();

        $gallery = $this->galleryRepository->findByUuid($gallery->uuid);
        
        if (!$gallery) {
            abort(404);
        }

        $this->galleryRepository->update($gallery, $validated);

        return redirect()->route('admin.galleries.show', $gallery->uuid)
            ->with('success', 'Gallery updated successfully!');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery = $this->galleryRepository->findByUuid($gallery->uuid);
        
        if (!$gallery) {
            abort(404);
        }

        $gallery->items()->each(function($item) {
            $this->galleryService->deleteItem($item);
        });

        $this->galleryRepository->delete($gallery);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Gallery deleted successfully!');
    }

    public function updateStatus(Request $request, Gallery $gallery): JsonResponse|RedirectResponse
    {
        $gallery = $this->galleryRepository->findByUuid($gallery->uuid);
        
        if (!$gallery) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $gallery->update(['status' => $validated['status']]);

        $statusLabel = ucfirst($validated['status']);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Gallery set to {$statusLabel}",
                'status' => $validated['status']
            ]);
        }

        return redirect()->back()
            ->with('success', "Gallery set to {$statusLabel}");
    }
}
