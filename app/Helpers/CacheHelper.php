<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    // Cache keys
    const HEADER_SETTINGS = 'header_settings';
    const ADMIN_SETTINGS = 'admin_settings';
    const APP_SETTINGS = 'app_settings';
    const EMAIL_SETTINGS = 'email_settings';
    const HOMEPAGE_SETTINGS = 'homepage_settings';
    const FOOTER_SETTINGS = 'footer_settings';
    const HEADER_CATEGORIES = 'header_categories';
    const FOOTER_CATEGORIES = 'footer_categories';
    const FOOTER_PAGES = 'footer_pages';
    const CATEGORIES_WITH_COUNT_ALL = 'categories_with_count_all';
    const CATEGORIES_WITH_COUNT_SIDEBAR = 'categories_with_count_sidebar';
    const PRICE_RANGE_ALL_PRODUCTS = 'price_range_all_products';

    /**
     * Clear all settings-related caches
     */
    public static function forgetAllSettings(): void
    {
        Cache::forget(self::HEADER_SETTINGS);
        Cache::forget(self::ADMIN_SETTINGS);
        Cache::forget(self::APP_SETTINGS);
        Cache::forget(self::EMAIL_SETTINGS);
        Cache::forget(self::HOMEPAGE_SETTINGS);
        Cache::forget(self::FOOTER_SETTINGS);
    }

    /**
     * Clear all category-related caches
     */
    public static function clearCategoryCaches(): void
    {
        Cache::forget(self::CATEGORIES_WITH_COUNT_ALL);
        Cache::forget(self::CATEGORIES_WITH_COUNT_SIDEBAR);
        Cache::forget(self::HEADER_CATEGORIES);
        Cache::forget(self::FOOTER_CATEGORIES);
    }

    /**
     * Clear price range caches
     *
     * @param int|null $categoryId Optional category ID for category-specific cache
     */
    public static function clearPriceRangeCaches(?int $categoryId = null): void
    {
        Cache::forget(self::PRICE_RANGE_ALL_PRODUCTS);
        if ($categoryId) {
            Cache::forget('price_range_category_' . $categoryId);
        }
    }

    /**
     * Clear price range cache for a specific category
     *
     * @param int $categoryId Category ID
     */
    public static function clearPriceRangeCacheForCategory(int $categoryId): void
    {
        Cache::forget('price_range_category_' . $categoryId);
    }
}
