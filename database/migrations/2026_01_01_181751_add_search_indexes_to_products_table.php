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
        Schema::table('products', function (Blueprint $table) {
            // Composite index for search queries (status + name prefix matching)
            // This helps with queries like: WHERE status = 1 AND name LIKE 'query%'
            if (!$this->hasIndex('products', 'idx_products_status_name_search')) {
                $table->index(['status', 'name'], 'idx_products_status_name_search');
            }

            // Composite index for slug search (status + slug)
            if (!$this->hasIndex('products', 'idx_products_status_slug_search')) {
                $table->index(['status', 'slug'], 'idx_products_status_slug_search');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if ($this->hasIndex('products', 'idx_products_status_name_search')) {
                $table->dropIndex('idx_products_status_name_search');
            }
            if ($this->hasIndex('products', 'idx_products_status_slug_search')) {
                $table->dropIndex('idx_products_status_slug_search');
            }
        });
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
                $result = $connection->select(
                    "SELECT COUNT(*) as count FROM sqlite_master 
                     WHERE type = 'index' AND name = ? AND tbl_name = ?",
                    [$index, $tableName]
                );
                return isset($result[0]) && $result[0]->count > 0;
            } else {
                $databaseName = $connection->getDatabaseName();
                $result = $connection->select(
                    "SELECT COUNT(*) as count FROM information_schema.statistics
                     WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                    [$databaseName, $tableName, $index]
                );
                return isset($result[0]) && $result[0]->count > 0;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
};
