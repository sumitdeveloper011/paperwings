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
            // Add index on discount_price for faster price range queries
            if (!$this->hasIndex('products', 'products_discount_price_index')) {
                $table->index('discount_price', 'products_discount_price_index');
            }
            
            // Add composite index for price range queries with status
            if (!$this->hasIndex('products', 'idx_products_status_prices')) {
                $table->index(['status', 'discount_price', 'total_price'], 'idx_products_status_prices');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if ($this->hasIndex('products', 'products_discount_price_index')) {
                $table->dropIndex('products_discount_price_index');
            }
            if ($this->hasIndex('products', 'idx_products_status_prices')) {
                $table->dropIndex('idx_products_status_prices');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function hasIndex(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT COUNT(*) as count 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?",
            [$databaseName, $table, $index]
        );
        
        return $result[0]->count > 0;
    }
};
