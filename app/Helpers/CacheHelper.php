<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    // Cache keys - organized by category
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
    const DASHBOARD_STATS = 'dashboard_stats';
    
    // Cache key prefixes for dynamic keys
    const PRICE_RANGE_CATEGORY_PREFIX = 'price_range_category_';
    const SEARCH_CACHE_PREFIX = 'search_';

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
            Cache::forget(self::PRICE_RANGE_CATEGORY_PREFIX . $categoryId);
        }
    }

    /**
     * Clear price range cache for a specific category
     *
     * @param int $categoryId Category ID
     */
    public static function clearPriceRangeCacheForCategory(int $categoryId): void
    {
        Cache::forget(self::PRICE_RANGE_CATEGORY_PREFIX . $categoryId);
    }

    /**
     * Clear all product-related caches
     * Call this when products are bulk updated or imported
     */
    public static function clearProductCaches(?int $categoryId = null): void
    {
        self::clearPriceRangeCaches($categoryId);
        self::clearCategoryCaches();
    }

    /**
     * Clear search-related caches
     * Note: Search caches have short TTL (5 minutes), but this can be used for immediate invalidation
     */
    public static function clearSearchCaches(): void
    {
        // If using cache tags (Redis/Memcached), you could use:
        // Cache::tags(['search'])->flush();
        // For now, we rely on TTL since search caches are dynamic
    }

    /**
     * Clear all page-related caches
     */
    public static function clearPageCaches(): void
    {
        Cache::forget(self::FOOTER_PAGES);
    }

    /**
     * Clear dashboard statistics cache
     */
    public static function clearDashboardStats(): void
    {
        Cache::forget(self::DASHBOARD_STATS);
    }

    /**
     * Clear all frontend caches (useful for admin actions)
     * Use with caution - this clears a lot of cache
     */
    public static function clearAllFrontendCaches(): void
    {
        self::forgetAllSettings();
        self::clearCategoryCaches();
        self::clearPriceRangeCaches();
        self::clearPageCaches();
    }

    /**
     * Get cache key for price range by category
     *
     * @param int $categoryId Category ID
     * @return string Cache key
     */
    public static function getPriceRangeCacheKey(?int $categoryId = null): string
    {
        if ($categoryId) {
            return self::PRICE_RANGE_CATEGORY_PREFIX . $categoryId;
        }
        return self::PRICE_RANGE_ALL_PRODUCTS;
    }
}
