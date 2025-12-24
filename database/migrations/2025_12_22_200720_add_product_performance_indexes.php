<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Check if indexes exist before adding
            $indexes = $this->getIndexes('products');
            
            // Individual indexes for common WHERE clauses
            if (!in_array('products_eposnow_category_id_index', $indexes)) {
                $table->index('eposnow_category_id', 'products_eposnow_category_id_index');
            }
            
            if (!in_array('products_product_type_index', $indexes)) {
                $table->index('product_type', 'products_product_type_index');
            }
            
            // Composite indexes for common query patterns
            // For: WHERE status = 1 AND product_type = X
            if (!in_array('products_status_type_index', $indexes)) {
                $table->index(['status', 'product_type'], 'products_status_type_index');
            }
            
            // For: WHERE status = 1 AND eposnow_category_id = X
            if (!in_array('products_status_category_index', $indexes)) {
                $table->index(['status', 'eposnow_category_id'], 'products_status_category_index');
            }
            
            // For: WHERE status = 1 AND product_type = X AND eposnow_category_id = Y
            if (!in_array('products_status_type_category_index', $indexes)) {
                $table->index(['status', 'product_type', 'eposnow_category_id'], 'products_status_type_category_index');
            }
            
            // For search queries: WHERE status = 1 AND (name LIKE ...)
            if (!in_array('products_status_name_index', $indexes)) {
                $table->index(['status', 'name'], 'products_status_name_index');
            }
            
            // Verify slug index exists (commonly used in WHERE clauses)
            if (!in_array('products_slug_index', $indexes) && !in_array('products_slug_unique', $indexes)) {
                $table->index('slug', 'products_slug_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $indexes = $this->getIndexes('products');
            
            if (in_array('products_eposnow_category_id_index', $indexes)) {
                $table->dropIndex('products_eposnow_category_id_index');
            }
            
            if (in_array('products_product_type_index', $indexes)) {
                $table->dropIndex('products_product_type_index');
            }
            
            if (in_array('products_status_type_index', $indexes)) {
                $table->dropIndex('products_status_type_index');
            }
            
            if (in_array('products_status_category_index', $indexes)) {
                $table->dropIndex('products_status_category_index');
            }
            
            if (in_array('products_status_type_category_index', $indexes)) {
                $table->dropIndex('products_status_type_category_index');
            }
            
            if (in_array('products_status_name_index', $indexes)) {
                $table->dropIndex('products_status_name_index');
            }
            
            if (in_array('products_slug_index', $indexes)) {
                $table->dropIndex('products_slug_index');
            }
        });
    }

    /**
     * Get existing indexes for a table
     */
    private function getIndexes(string $table): array
    {
        $indexes = DB::select("SHOW INDEXES FROM `{$table}`");
        return array_map(function($index) {
            return $index->Key_name;
        }, $indexes);
    }
};
