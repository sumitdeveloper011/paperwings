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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('eposnow_product_id')->nullable();
            $table->unsignedBigInteger('eposnow_category_id')->nullable();
            $table->unsignedBigInteger('eposnow_brand_id')->nullable();
            $table->text('barcode')->nullable();
            $table->integer('stock')->nullable();
            $table->integer('product_type')->nullable();
            $table->integer('sort_order')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->enum('discount_type', ['none', 'direct', 'percentage'])->default('none');
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->text('short_description')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->softDeletes();
            $table->timestamps();

            $table->index('category_id');
            $table->index('brand_id');
            $table->index('eposnow_category_id');
            $table->index('eposnow_brand_id');
            $table->index('status');
            $table->index('name');
            $table->index('slug');
            $table->index('total_price');
            $table->index('discount_price', 'products_discount_price_index');
            $table->index('created_at');
            $table->index('product_type');
            $table->index('discount_type');
            $table->index(['status', 'name'], 'idx_products_status_name_search');
            $table->index(['status', 'slug'], 'idx_products_status_slug_search');
            $table->index(['status', 'product_type'], 'idx_products_status_type');
            $table->index(['status', 'total_price'], 'idx_products_status_total_price');
            $table->index(['status', 'discount_price', 'total_price'], 'idx_products_status_prices');
            $table->index(['category_id', 'status'], 'idx_products_category_status');
            $table->index(['brand_id', 'status'], 'idx_products_brand_status');
            $table->index(['status', 'eposnow_category_id'], 'idx_products_eposnow_category_status');
            $table->index(['status', 'product_type', 'eposnow_category_id'], 'idx_products_status_type_category');
            $table->index(['status', 'product_type', 'sort_order'], 'idx_products_bundles');
            $table->index(['created_at', 'status'], 'idx_products_created_status');
            $table->index(['total_price', 'status'], 'idx_products_price_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
