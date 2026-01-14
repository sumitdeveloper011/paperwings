-- Safe script to truncate categories and products tables
-- This script temporarily disables foreign key checks, truncates the tables, then re-enables them

-- Step 1: Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Step 2: Truncate products table first (since it references categories)
TRUNCATE TABLE `products`;

-- Step 3: Truncate categories table
TRUNCATE TABLE `categories`;

-- Step 4: Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Note: This will also cascade delete related records in:
-- - product_images
-- - product_accordions
-- - product_faqs
-- - product_reviews
-- - product_questions
-- - product_tags
-- - cart_items
-- - order_items
-- - wishlists
-- - subcategories (if cascade is set)
-- etc.
