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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('For authenticated users');
            $table->string('session_id')->nullable()->comment('For guest users');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->comment('Price snapshot at time of adding to cart');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('session_id');
            $table->index('product_id');

            // Composite indexes for better query performance
            $table->index(['user_id', 'product_id']);
            $table->index(['session_id', 'product_id']);

            // Note: Unique constraints are handled at application level
            // We can't use database unique constraints because user_id and session_id can both be null
            // The CartController handles duplicate prevention
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
