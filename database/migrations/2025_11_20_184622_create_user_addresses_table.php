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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['billing', 'shipping'])->default('shipping');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('street_address');
            $table->string('street_address_2')->nullable();
            $table->string('suburb')->nullable();
            $table->string('city');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->string('zip_code');
            $table->string('country');
            $table->boolean('is_default')->default(false)->comment('Default address for this type');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');

            // Indexes
            $table->index('user_id');
            $table->index('region_id');
            $table->index(['user_id', 'type']);
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
