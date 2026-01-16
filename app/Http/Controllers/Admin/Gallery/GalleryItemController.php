<?php

namespace App\Http\Controllers\Admin\Gallery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Gallery\StoreGalleryItemRequest;
use App\Http\Requests\Admin\Gallery\UpdateGalleryItemRequest;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Services\GalleryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryItemController extends Controller
{
    protected GalleryService $galleryService;

    public function __construct(GalleryService $galleryService)
    {
        $this->galleryService = $galleryService;
    }

    public function store(StoreGalleryItemRequest $request, Gallery $gallery): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        try {
            if ($validated['type'] === 'image') {
                $item = $this->galleryService->uploadImageItem(
                    $gallery,
                    $request->file('image'),
                    [
                        'title' => $validated['title'] ?? null,
                        'description' => $validated['description'] ?? null,
                        'is_featured' => $validated['is_featured'] ?? false,
                        'alt_text' => $validated['alt_text'] ?? null,
                    ]
                );
            } else {
                $itemData = [
                    'title' => $validated['title'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'is_featured' => $validated['is_featured'] ?? false,
                ];

                if (isset($validated['video_embed_code'])) {
                    $itemData['video_embed_code'] = $validated['video_embed_code'];
                }

                if (isset($validated['video_url'])) {
                    $itemData['video_url'] = $validated['video_url'];
                }

                if ($request->hasFile('thumbnail')) {
                    $thumbnailPath = app(\App\Services\ImageService::class)->uploadImage(
                        $request->file('thumbnail'),
                        'galleries',
                        $gallery->uuid,
                        null,
                        true
                    );
                    $itemData['thumbnail_path'] = $thumbnailPath;
                }

                $item = $this->galleryService->uploadVideoItem($gallery, $itemData);
            }

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item added successfully!',
                    'item' => $item->load('gallery'),
                ]);
            }

            return redirect()->route('admin.galleries.show', $gallery->uuid)
                ->with('success', 'Item added successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('admin.galleries.show', $gallery->uuid)
                ->with('error', $e->getMessage());
        }
    }

    public function update(UpdateGalleryItemRequest $request, Gallery $gallery, GalleryItem $galleryItem): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        try {
            $updateData = [];

            if (isset($validated['title'])) {
                $updateData['title'] = $validated['title'];
            }

            if (isset($validated['description'])) {
                $updateData['description'] = $validated['description'];
            }

            if (isset($validated['alt_text'])) {
                $updateData['alt_text'] = $validated['alt_text'];
            }

            if (isset($validated['order'])) {
                $updateData['order'] = $validated['order'];
            }

            if (isset($validated['is_featured'])) {
                $updateData['is_featured'] = $validated['is_featured'];
            }

            if ($galleryItem->type === 'video') {
                if (isset($validated['video_embed_code'])) {
                    $updateData['video_embed_code'] = $validated['video_embed_code'];
                }

                if (isset($validated['video_url'])) {
                    $updateData['video_url'] = $validated['video_url'];
                }

                if ($request->hasFile('thumbnail')) {
                    $thumbnailPath = app(\App\Services\ImageService::class)->uploadImage(
                        $request->file('thumbnail'),
                        'galleries',
                        $gallery->uuid,
                        $galleryItem->thumbnail_path,
                        true
                    );
                    $updateData['thumbnail_path'] = $thumbnailPath;
                }
            } elseif ($galleryItem->type === 'image' && $request->hasFile('image')) {
                $imagePath = app(\App\Services\ImageService::class)->uploadImage(
                    $request->file('image'),
                    'galleries',
                    $gallery->uuid,
                    $galleryItem->image_path,
                    true
                );
                $updateData['image_path'] = $imagePath;
            }

            $this->galleryService->updateItem($galleryItem, $updateData);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item updated successfully!',
                    'item' => $galleryItem->fresh()->load('gallery'),
                ]);
            }

            return redirect()->route('admin.galleries.show', $gallery->uuid)
                ->with('success', 'Item updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('admin.galleries.show', $gallery->uuid)
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Gallery $gallery, GalleryItem $galleryItem): RedirectResponse|JsonResponse
    {
        try {
            $this->galleryService->deleteItem($galleryItem);

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item deleted successfully!',
                ]);
            }

            return redirect()->route('admin.galleries.show', $gallery->uuid)
                ->with('success', 'Item deleted successfully!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('admin.galleries.show', $gallery->uuid)
                ->with('error', $e->getMessage());
        }
    }

    public function reorder(Request $request, Gallery $gallery): JsonResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:gallery_items,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        try {
            $itemOrders = [];
            foreach ($request->items as $item) {
                $itemOrders[$item['id']] = $item['order'];
            }

            $this->galleryService->reorderItems($gallery, $itemOrders);

            return response()->json([
                'success' => true,
                'message' => 'Items reordered successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function setFeatured(Gallery $gallery, GalleryItem $galleryItem): JsonResponse
    {
        try {
            $this->galleryService->setFeaturedItem($gallery, $galleryItem);

            return response()->json([
                'success' => true,
                'message' => 'Featured item set successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
