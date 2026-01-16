<?php

namespace App\Services;

use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryService
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function createGallery(array $data): Gallery
    {
        $gallery = Gallery::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? 'general',
            'status' => $data['status'] ?? 'active',
            'created_by' => auth()->id(),
        ]);

        return $gallery;
    }

    public function updateGallery(Gallery $gallery, array $data): Gallery
    {
        $gallery->update([
            'name' => $data['name'] ?? $gallery->name,
            'description' => $data['description'] ?? $gallery->description,
            'category' => $data['category'] ?? $gallery->category,
            'status' => $data['status'] ?? $gallery->status,
        ]);

        return $gallery->fresh();
    }

    public function uploadImageItem(Gallery $gallery, UploadedFile $file, array $data = []): GalleryItem
    {
        $imagePath = $this->imageService->uploadImage(
            $file,
            'galleries',
            $gallery->uuid,
            null,
            true
        );

        if (!$imagePath) {
            throw new \Exception('Failed to upload image');
        }

        $maxOrder = $gallery->items()->max('order') ?? 0;

        $item = GalleryItem::create([
            'gallery_id' => $gallery->id,
            'type' => 'image',
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'image_path' => $imagePath,
            'order' => $maxOrder + 1,
            'is_featured' => $data['is_featured'] ?? false,
            'alt_text' => $data['alt_text'] ?? null,
        ]);

        return $item;
    }

    public function uploadVideoItem(Gallery $gallery, array $data): GalleryItem
    {
        $maxOrder = $gallery->items()->max('order') ?? 0;

        $itemData = [
            'gallery_id' => $gallery->id,
            'type' => 'video',
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'order' => $maxOrder + 1,
            'is_featured' => $data['is_featured'] ?? false,
        ];

        if (isset($data['video_embed_code'])) {
            $itemData['video_embed_code'] = $this->sanitizeEmbedCode($data['video_embed_code']);
        }

        if (isset($data['video_url'])) {
            $itemData['video_url'] = $data['video_url'];
        }

        if (isset($data['thumbnail_path'])) {
            $itemData['thumbnail_path'] = $data['thumbnail_path'];
        }

        $item = GalleryItem::create($itemData);

        return $item;
    }

    public function updateItem(GalleryItem $item, array $data): GalleryItem
    {
        $updateData = [];

        if (isset($data['title'])) {
            $updateData['title'] = $data['title'];
        }

        if (isset($data['description'])) {
            $updateData['description'] = $data['description'];
        }

        if (isset($data['alt_text'])) {
            $updateData['alt_text'] = $data['alt_text'];
        }

        if (isset($data['order'])) {
            $updateData['order'] = $data['order'];
        }

        if (isset($data['is_featured'])) {
            $updateData['is_featured'] = $data['is_featured'];
        }

        if ($item->type === 'video') {
            if (isset($data['video_embed_code'])) {
                $updateData['video_embed_code'] = $this->sanitizeEmbedCode($data['video_embed_code']);
            }

            if (isset($data['video_url'])) {
                $updateData['video_url'] = $data['video_url'];
            }
        }

        $item->update($updateData);

        return $item->fresh();
    }

    public function deleteItem(GalleryItem $item): bool
    {
        if ($item->image_path) {
            $this->imageService->deleteImage($item->image_path);
        }

        if ($item->thumbnail_path) {
            $this->imageService->deleteImage($item->thumbnail_path);
        }

        return $item->delete();
    }

    public function reorderItems(Gallery $gallery, array $itemOrders): bool
    {
        foreach ($itemOrders as $itemId => $order) {
            GalleryItem::where('id', $itemId)
                ->where('gallery_id', $gallery->id)
                ->update(['order' => $order]);
        }

        return true;
    }

    public function setFeaturedItem(Gallery $gallery, GalleryItem $item): bool
    {
        $gallery->items()->update(['is_featured' => false]);
        $item->update(['is_featured' => true]);
        $gallery->update(['cover_image_id' => $item->id]);

        return true;
    }

    protected function sanitizeEmbedCode(string $embedCode): string
    {
        $allowedDomains = ['youtube.com', 'youtu.be', 'vimeo.com'];
        
        $domainsPattern = implode('|', array_map('preg_quote', $allowedDomains));
        
        if (!preg_match('/(' . $domainsPattern . ')/i', $embedCode)) {
            throw new \InvalidArgumentException('Embed code must be from YouTube or Vimeo');
        }

        $embedCode = strip_tags($embedCode, '<iframe><embed><object>');
        
        return $embedCode;
    }

    public function validateVideoUrl(string $url): bool
    {
        $allowedExtensions = ['mp4', 'webm', 'ogg'];
        $urlExtension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        return in_array($urlExtension, $allowedExtensions);
    }
}
