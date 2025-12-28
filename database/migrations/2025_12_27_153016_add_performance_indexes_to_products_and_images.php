<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to products table for better query performance
        Schema::table('products', function (Blueprint $table) {
            // Composite index for homepage queries (status + product_type)
            if (!$this->hasIndex('products', 'idx_products_status_type')) {
                $table->index(['status', 'product_type'], 'idx_products_status_type');
            }

            // Composite index for category filtering
            if (!$this->hasIndex('products', 'idx_products_category_status')) {
                $table->index(['category_id', 'status'], 'idx_products_category_status');
            }

            // Index for new arrivals query
            if (!$this->hasIndex('products', 'idx_products_created_status')) {
                $table->index(['created_at', 'status'], 'idx_products_created_status');
            }

            // Index for slug lookup (product detail page)
            // Note: Original migration already creates index on slug (auto-named)
            // Only create named index if it doesn't exist and no index exists on slug column
            if (!$this->hasIndex('products', 'products_slug_index')) {
                // Check if any index exists on slug column (to avoid duplicate)
                if (!$this->hasIndexOnColumn('products', 'slug')) {
                    $table->index('slug', 'products_slug_index');
                }
            }

            // Index for brand filtering
            if (!$this->hasIndex('products', 'idx_products_brand_status')) {
                $table->index(['brand_id', 'status'], 'idx_products_brand_status');
            }

            // Index for price filtering
            if (!$this->hasIndex('products', 'idx_products_price_status')) {
                $table->index(['total_price', 'status'], 'idx_products_price_status');
            }
        });

        // Add indexes to products_images table (note: table name is products_images, not product_images)
        if (Schema::hasTable('products_images')) {
            Schema::table('products_images', function (Blueprint $table) {
                // Composite index for withFirstImage scope
                if (!$this->hasIndex('products_images', 'idx_images_product_id')) {
                    $table->index(['product_id', 'id'], 'idx_images_product_id');
                }
            });
        }

        // Add indexes to categories table
        // Note: Categories table doesn't have 'order' column, ordered() scope uses 'name'
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                // Index for active categories (status already has index, but adding composite if needed)
                // Since ordered() uses 'name', we'll just ensure status index exists
                // Status index already exists from original migration, so skip
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status_type');
            $table->dropIndex('idx_products_category_status');
            $table->dropIndex('idx_products_created_status');
            if ($this->hasIndex('products', 'products_slug_index')) {
                $table->dropIndex('products_slug_index');
            }
            $table->dropIndex('idx_products_brand_status');
            $table->dropIndex('idx_products_price_status');
        });

        Schema::table('products_images', function (Blueprint $table) {
            $table->dropIndex('idx_images_product_id');
        });

        // Categories index removal not needed (no new index added)
    }

    /**
     * Check if index exists (supports both MySQL and SQLite)
     */
    private function hasIndex($table, $index): bool
    {
        try {
            $connection = Schema::getConnection();
            $driverName = $connection->getDriverName();
            $tableName = $connection->getTablePrefix() . $table;

            if ($driverName === 'sqlite') {
                // SQLite: Check indexes using sqlite_master
                $result = $connection->select(
                    "SELECT COUNT(*) as count FROM sqlite_master 
                     WHERE type = 'index' AND name = ? AND tbl_name = ?",
                    [$index, $tableName]
                );
                return isset($result[0]) && $result[0]->count > 0;
            } else {
                // MySQL/PostgreSQL: Use information_schema
                $databaseName = $connection->getDatabaseName();
                $result = $connection->select(
                    "SELECT COUNT(*) as count FROM information_schema.statistics
                     WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                    [$databaseName, $tableName, $index]
                );
                return isset($result[0]) && $result[0]->count > 0;
            }
        } catch (\Exception $e) {
            // If check fails, assume index doesn't exist (safer to try creating)
            return false;
        }
    }

    /**
     * Check if any index exists on a column (for SQLite compatibility)
     */
    private function hasIndexOnColumn($table, $column): bool
    {
        try {
            $connection = Schema::getConnection();
            $driverName = $connection->getDriverName();
            $tableName = $connection->getTablePrefix() . $table;

            if ($driverName === 'sqlite') {
                // SQLite: Check if any index exists on this column
                $indexes = $connection->select(
                    "SELECT sql FROM sqlite_master 
                     WHERE type = 'index' AND tbl_name = ? AND sql LIKE ?",
                    [$tableName, "%{$column}%"]
                );
                return count($indexes) > 0;
            } else {
                // MySQL: Check information_schema
                $databaseName = $connection->getDatabaseName();
                $result = $connection->select(
                    "SELECT COUNT(*) as count FROM information_schema.statistics
                     WHERE table_schema = ? AND table_name = ? AND column_name = ?",
                    [$databaseName, $tableName, $column]
                );
                return isset($result[0]) && $result[0]->count > 0;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
};
