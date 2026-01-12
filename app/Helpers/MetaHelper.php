<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class MetaHelper
{
    /**
     * Auto-fill meta fields from main fields
     *
     * @param array $data Data array with name/title, description, etc.
     * @param object|null $model Optional model instance for fallback values
     * @return array Updated data array
     */
    public static function autoFillMetaFields(array $data, ?object $model = null): array
    {
        // Meta Title: Use name/title if empty
        if (empty($data['meta_title'])) {
            $data['meta_title'] = $data['name'] ?? $data['title'] ?? ($model ? ($model->name ?? $model->title ?? null) : null);
        }

        // Meta Description: Use short_description/description if empty
        if (empty($data['meta_description'])) {
            $description = $data['short_description'] ?? $data['description'] ?? null;
            if (!$description && $model) {
                $description = $model->short_description ?? $model->description ?? null;
            }
            if ($description) {
                $data['meta_description'] = self::truncateDescription($description, 160);
            }
        }

        // Meta Keywords: Generate from name/title and category if empty
        if (empty($data['meta_keywords'])) {
            $name = $data['name'] ?? $data['title'] ?? ($model ? ($model->name ?? $model->title ?? null) : null);
            $categoryId = $data['category_id'] ?? ($model ? $model->category_id : null);
            if ($name) {
                $data['meta_keywords'] = self::generateMetaKeywords($name, $categoryId);
            }
        }

        return $data;
    }

    /**
     * Truncate description for meta description (max 160 characters)
     *
     * @param string|null $description
     * @param int $maxLength
     * @return string|null
     */
    public static function truncateDescription(?string $description, int $maxLength = 160): ?string
    {
        if (empty($description)) {
            return null;
        }

        // Remove HTML tags
        $description = strip_tags($description);

        // Trim whitespace
        $description = trim($description);

        if (strlen($description) <= $maxLength) {
            return $description;
        }

        // Truncate and add ellipsis
        return substr($description, 0, $maxLength - 3) . '...';
    }

    /**
     * Generate meta keywords from name and category
     *
     * @param string $name
     * @param int|null $categoryId
     * @return string|null
     */
    public static function generateMetaKeywords(string $name, ?int $categoryId = null): ?string
    {
        $keywords = [];

        // Add name words
        $nameWords = explode(' ', strtolower($name));
        $keywords = array_merge($keywords, array_filter($nameWords, function($word) {
            return strlen($word) > 3; // Only words longer than 3 characters
        }));

        // Add category name if available
        if ($categoryId) {
            try {
                $category = \App\Models\Category::find($categoryId);
                if ($category && $category->name) {
                    $categoryWords = explode(' ', strtolower($category->name));
                    $keywords = array_merge($keywords, array_filter($categoryWords, function($word) {
                        return strlen($word) > 3;
                    }));
                }
            } catch (\Exception $e) {
                // Category not found, skip
            }
        }

        // Remove duplicates and limit to 10 keywords
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);

        return !empty($keywords) ? implode(', ', $keywords) : null;
    }
}
