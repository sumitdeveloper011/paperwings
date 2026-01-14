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
            // Add index on deleted_at for soft deletes queries
            $table->index('deleted_at', 'idx_products_deleted_at');
            
            // Add composite index for common query pattern: status + deleted_at + name
            // This optimizes queries like: where status = 1 and deleted_at is null order by name
            $table->index(['status', 'deleted_at', 'name'], 'idx_products_status_deleted_name');
            
            // Add composite index for bundles query: product_type + status + deleted_at + name
            // This optimizes queries like: where product_type = 4 and status = 1 and deleted_at is null order by name
            $table->index(['product_type', 'status', 'deleted_at', 'name'], 'idx_products_type_status_deleted_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_deleted_at');
            $table->dropIndex('idx_products_status_deleted_name');
            $table->dropIndex('idx_products_type_status_deleted_name');
        });
    }
};
