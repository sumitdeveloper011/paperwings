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
        Schema::create('shipping_prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('region_id')->constrained('regions')->onDelete('cascade');
            $table->decimal('shipping_price', 10, 2)->default(0.00);
            $table->decimal('free_shipping_minimum', 10, 2)->nullable()->comment('Minimum order amount for free shipping');
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('region_id');
            // Ensure one shipping price per region
            $table->unique('region_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_prices');
    }
};
