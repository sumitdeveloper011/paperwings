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
            $table->integer('sort_order')->nullable()->after('product_type');
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_price');
            $table->index(['status', 'product_type', 'sort_order'], 'idx_products_bundles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_bundles');
            $table->dropColumn(['sort_order', 'discount_percentage']);
        });
    }
};
