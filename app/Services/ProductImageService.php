<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

class ProductImageService
{
    /**
     * Get first image for multiple products efficiently
     * Uses caching and eager loading to prevent N+1 queries
     *
     * @param Collection|SupportCollection|array $productIds
     * @param bool $useCache
     * @return Collection Keyed by product_id
     */
    public static function getFirstImagesForProducts($productIds, bool $useCache = true): Collection
    {
        if (empty($productIds)) {
            return collect();
        }

        // Ensure we have a collection
        if (is_array($productIds)) {
            $productIds = collect($productIds);
        }

        // Remove duplicates and filter nulls
        $productIds = $productIds->filter()->unique()->values();

        if ($productIds->isEmpty()) {
            return collect();
        }

        // Create cache key
        $cacheKey = 'product_images_first_' . md5($productIds->sort()->implode('_'));

        if ($useCache) {
            return Cache::remember($cacheKey, 3600, function () use ($productIds) {
                return self::fetchFirstImages($productIds);
            });
        }

        return self::fetchFirstImages($productIds);
    }

    /**
     * Fetch first images from database
     *
     * @param Collection $productIds
     * @return Collection
     */
    private static function fetchFirstImages(Collection $productIds): Collection
    {
        // Get first image ID for each product
        $firstImageIds = \DB::table('products_images')
            ->select('product_id', \DB::raw('MIN(id) as min_id'))
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id')
            ->pluck('min_id', 'product_id');

        if ($firstImageIds->isEmpty()) {
            return collect();
        }

        // Fetch the actual images
        $images = ProductImage::whereIn('id', $firstImageIds->values())
            ->select('id', 'product_id', 'image')
            ->get()
            ->keyBy('product_id');

        return $images;
    }

    /**
     * Get first image URL for a single product
     * Checks if images are already loaded to prevent N+1
     *
     * @param mixed $product Product model or product ID
     * @param Collection|null $preloadedImages Pre-loaded images collection
     * @return string
     */
    public static function getFirstImageUrl($product, ?Collection $preloadedImages = null): string
    {
        // If product is a model and images are already loaded
        if (is_object($product) && method_exists($product, 'relationLoaded')) {
            if ($product->relationLoaded('images') && $product->images->isNotEmpty()) {
                return $product->images->first()->image_url;
            }
        }

        // Use preloaded images if provided
        if ($preloadedImages) {
            $productId = is_object($product) ? $product->id : $product;
            $image = $preloadedImages->get($productId);
            if ($image) {
                return $image->image_url;
            }
        }

        // Fallback: query database (should be avoided)
        $productId = is_object($product) ? $product->id : $product;
        $image = self::getFirstImagesForProducts(collect([$productId]), false)->first();
        
        if ($image) {
            return $image->image_url;
        }

        return asset('assets/images/placeholder.jpg');
    }

    /**
     * Clear cache for product images
     *
     * @param array|Collection|null $productIds
     * @return void
     */
    public static function clearCache($productIds = null): void
    {
        if ($productIds) {
            $productIds = is_array($productIds) ? collect($productIds) : $productIds;
            $cacheKey = 'product_images_first_' . md5($productIds->sort()->implode('_'));
            Cache::forget($cacheKey);
        } else {
            // Clear all product image caches (use tags if available)
            Cache::flush(); // Or use Cache::tags(['product_images'])->flush() if tags are configured
        }
    }

    /**
     * Attach first images to products collection
     *
     * @param Collection $products
     * @return Collection
     */
    public static function attachFirstImagesToProducts(Collection $products): Collection
    {
        if ($products->isEmpty()) {
            return $products;
        }

        $productIds = $products->pluck('id')->unique();
        $images = self::getFirstImagesForProducts($productIds);

        return $products->map(function ($product) use ($images) {
            $image = $images->get($product->id);
            $product->setAttribute('main_image', $image ? $image->image_url : asset('assets/images/placeholder.jpg'));
            return $product;
        });
    }
}

