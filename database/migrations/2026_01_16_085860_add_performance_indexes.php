<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds additional indexes for frequently queried columns to improve performance
     */
    public function up(): void
    {
        // Add index to order_items for order_id + product_id queries
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                // Composite index for order details queries
                if (!$this->indexExists('order_items', 'idx_order_items_order_product')) {
                    $table->index(['order_id', 'product_id'], 'idx_order_items_order_product');
                }
            });
        }

        // Add index to categories for status + slug queries
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                // Composite index for active category lookups by slug
                if (!$this->indexExists('categories', 'idx_categories_status_slug')) {
                    $table->index(['status', 'slug'], 'idx_categories_status_slug');
                }
            });
        }

        // Add index to orders for stripe_payment_intent_id lookups
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                // Index for payment verification queries
                if (!$this->indexExists('orders', 'idx_orders_stripe_payment_intent')) {
                    $table->index('stripe_payment_intent_id', 'idx_orders_stripe_payment_intent');
                }
            });
        }

        // Add index to cart_items for user_id + product_id lookups (if not exists)
        if (Schema::hasTable('cart_items')) {
            Schema::table('cart_items', function (Blueprint $table) {
                // This composite index might already exist, but ensure it does
                if (!$this->indexExists('cart_items', 'cart_items_user_id_product_id_index')) {
                    $table->index(['user_id', 'product_id'], 'cart_items_user_id_product_id_index');
                }
            });
        }

        // Add composite index to products for category queries with status and soft deletes
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                // Optimizes queries filtering by category_id, status, and deleted_at
                // Used in category listings, footer categories, and similar queries
                if (!$this->indexExists('products', 'idx_products_category_status_deleted')) {
                    $table->index(['category_id', 'status', 'deleted_at'], 'idx_products_category_status_deleted');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropIndex('idx_order_items_order_product');
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropIndex('idx_categories_status_slug');
            });
        }

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('idx_orders_stripe_payment_intent');
            });
        }

        if (Schema::hasTable('cart_items')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropIndex('cart_items_user_id_product_id_index');
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex('idx_products_category_status_deleted');
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $driverName = $connection->getDriverName();
        $tableName = $connection->getTablePrefix() . $table;
        
        if ($driverName === 'sqlite') {
            $result = $connection->select(
                "SELECT COUNT(*) as count FROM sqlite_master 
                 WHERE type = 'index' AND name = ? AND tbl_name = ?",
                [$index, $tableName]
            );
        } else {
            $databaseName = $connection->getDatabaseName();
            $result = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$databaseName, $tableName, $index]
            );
        }
        
        return $result[0]->count > 0;
    }
};
