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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->text('review');
            $table->integer('rating')->default(5)->comment('1-5 stars');
            $table->string('image')->nullable()->comment('Customer photo');
            $table->string('designation')->nullable()->comment('Customer designation/position');
            $table->tinyInteger('status')->default(1)->comment('1: Active, 0: Inactive');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('status');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
