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
            // Add indexes for fast search (only if they don't exist)
            if (!$this->indexExists('products', 'products_name_index')) {
                $table->index('name');
            }
            if (!$this->indexExists('products', 'products_slug_index')) {
                $table->index('slug');
            }
            if (!$this->indexExists('products', 'products_total_price_index')) {
                $table->index('total_price');
            }
            if (!$this->indexExists('products', 'products_created_at_index')) {
                $table->index('created_at');
            }

            // Composite indexes for common search patterns
            if (!$this->indexExists('products', 'products_status_name_index')) {
                $table->index(['status', 'name']);
            }
            if (!$this->indexExists('products', 'products_status_total_price_index')) {
                $table->index(['status', 'total_price']);
            }
            if (!$this->indexExists('products', 'products_category_id_status_index')) {
                $table->index(['category_id', 'status']);
            }
        });
    }

    private function indexExists($table, $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();

        try {
            $result = DB::select(
                "SELECT COUNT(*) as count FROM information_schema.statistics
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$databaseName, $table, $indexName]
            );

            return isset($result[0]) && $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['status']);
            $table->dropIndex(['total_price']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'name']);
            $table->dropIndex(['status', 'total_price']);
            $table->dropIndex(['category_id', 'status']);
        });
    }
};

