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
        // Drop old foreign key constraint
        Schema::table('product_bundle_items', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
        });

        // Add new foreign key pointing to products table
        Schema::table('product_bundle_items', function (Blueprint $table) {
            $table->foreign('bundle_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_bundle_items', function (Blueprint $table) {
            $table->dropForeign(['bundle_id']);
        });

        // Restore old foreign key (if product_bundles table still exists)
        if (Schema::hasTable('product_bundles')) {
            Schema::table('product_bundle_items', function (Blueprint $table) {
                $table->foreign('bundle_id')
                      ->references('id')
                      ->on('product_bundles')
                      ->onDelete('cascade');
            });
        }
    }
};
