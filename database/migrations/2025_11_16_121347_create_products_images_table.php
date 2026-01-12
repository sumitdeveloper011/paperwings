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
        Schema::create('products_images', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('eposnow_product_id')->nullable();
            $table->string('image');
            $table->timestamps();

            // Indexes for performance
            $table->index('product_id');
            $table->index(['product_id', 'id'], 'idx_images_product_id'); // For withFirstImage scope
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_images');
    }
};
