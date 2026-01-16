<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Repositories\Interfaces\GalleryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    protected GalleryRepositoryInterface $galleryRepository;

    public function __construct(GalleryRepositoryInterface $galleryRepository)
    {
        $this->galleryRepository = $galleryRepository;
    }

    public function index(Request $request): View
    {
        $title = 'Gallery';
        $category = $request->get('category', '');
        
        $query = Gallery::query()
            ->where('status', 'active')
            ->with(['coverImage', 'items' => function($q) {
                $q->orderBy('order');
            }]);

        if ($category !== '') {
            $query->where('category', $category);
        }

        $galleries = $query->orderBy('created_at', 'desc')->paginate(12);

        $categories = [
            'general' => 'General',
            'products' => 'Products',
            'events' => 'Events',
            'portfolio' => 'Portfolio',
            'other' => 'Other'
        ];

        return view('frontend.gallery.index', compact('title', 'galleries', 'category', 'categories'));
    }

    public function show(string $slug): View
    {
        $gallery = $this->galleryRepository->findBySlug($slug);

        if (!$gallery || $gallery->status !== 'active') {
            abort(404);
        }

        $gallery->load(['items' => function($query) {
            $query->orderBy('order');
        }, 'coverImage', 'creator']);

        $title = $gallery->title ?? 'Gallery';

        return view('frontend.gallery.show', compact('title', 'gallery'));
    }
}
