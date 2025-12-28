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
            $table->bigInteger('barcode')->nullable();
            $table->integer('stock')->nullable();
            $table->integer('product_type')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->timestamps();
            
            // Basic indexes
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('eposnow_category_id');
            $table->index('eposnow_brand_id');
            $table->index('status');
            
            // Search indexes
            $table->index('name');
            $table->index('slug');
            $table->index('total_price');
            $table->index('created_at');
            $table->index('product_type');
            
            // Composite indexes for common query patterns
            $table->index(['status', 'name']);
            $table->index(['status', 'total_price']);
            $table->index(['category_id', 'status']);
            $table->index(['status', 'product_type']);
            $table->index(['status', 'eposnow_category_id']);
            $table->index(['status', 'product_type', 'eposnow_category_id']);
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
