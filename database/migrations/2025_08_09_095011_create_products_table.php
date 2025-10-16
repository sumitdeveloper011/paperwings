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
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');

            // $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            // $table->foreignId('subcategory_id')->nullable()->constrained('subcategories')->onDelete('set null');
            // $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->integer('brand_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('total_price', 10, 2)->nullable(); // Price including tax
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->bigInteger('barcode')->nullable();
            $table->json('accordion_data')->nullable(); // For accordion sections
            $table->json('images')->nullable(); // For multiple images
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->timestamps();

            // $table->index(['category_id', 'subcategory_id', 'brand_id']);
            $table->index(['category_id']);
            $table->index(['status']);
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
