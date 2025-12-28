<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

class ProductImageService
{
    // Get first image for multiple products efficiently
    public static function getFirstImagesForProducts($productIds, bool $useCache = true): Collection
    {
        if (empty($productIds)) {
            return collect();
        }

        if (is_array($productIds)) {
            $productIds = collect($productIds);
        }

        $productIds = $productIds->filter()->unique()->values();

        if ($productIds->isEmpty()) {
            return collect();
        }

        $cacheKey = 'product_images_first_' . md5($productIds->sort()->implode('_'));

        if ($useCache) {
            return Cache::remember($cacheKey, 3600, function () use ($productIds) {
                return self::fetchFirstImages($productIds);
            });
        }

        return self::fetchFirstImages($productIds);
    }

    // Fetch first images from database
    private static function fetchFirstImages($productIds): Collection
    {
        if ($productIds instanceof Collection || $productIds instanceof SupportCollection) {
            $productIdsArray = $productIds->toArray();
        } else {
            $productIdsArray = $productIds;
        }
        
        $firstImageIds = \DB::table('products_images')
            ->select('product_id', \DB::raw('MIN(id) as min_id'))
            ->whereIn('product_id', $productIdsArray)
            ->groupBy('product_id')
            ->pluck('min_id', 'product_id');

        if ($firstImageIds->isEmpty()) {
            return ProductImage::whereRaw('1 = 0')->get();
        }

        $images = ProductImage::whereIn('id', $firstImageIds->values())
            ->select('id', 'product_id', 'image')
            ->get()
            ->keyBy('product_id');

        return $images;
    }

    // Get first image URL for a single product
    public static function getFirstImageUrl($product, ?Collection $preloadedImages = null): string
    {
        if (is_object($product) && method_exists($product, 'relationLoaded')) {
            if ($product->relationLoaded('images') && $product->images->isNotEmpty()) {
                return $product->images->first()->image_url;
            }
        }

        if ($preloadedImages) {
            $productId = is_object($product) ? $product->id : $product;
            $image = $preloadedImages->get($productId);
            if ($image) {
                return $image->image_url;
            }
        }

        $productId = is_object($product) ? $product->id : $product;
        $image = self::getFirstImagesForProducts(collect([$productId]), false)->first();
        
        if ($image) {
            return $image->image_url;
        }

        return asset('assets/images/placeholder.jpg');
    }

    // Clear cache for product images
    public static function clearCache($productIds = null): void
    {
        if ($productIds) {
            $productIds = is_array($productIds) ? collect($productIds) : $productIds;
            $cacheKey = 'product_images_first_' . md5($productIds->sort()->implode('_'));
            Cache::forget($cacheKey);
        } else {
            Cache::flush();
        }
    }

    // Attach first images to products collection
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

