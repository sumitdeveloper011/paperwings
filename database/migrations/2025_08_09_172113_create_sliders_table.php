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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('image');
            $table->string('heading');
            $table->string('sub_heading')->nullable();
            $table->json('buttons')->nullable(); // Store up to 2 buttons with name and url
            $table->integer('sort_order')->nullable()->default(null);
            $table->tinyInteger('status')->default(1)->comment('0 means de-activate,1 means active');
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};