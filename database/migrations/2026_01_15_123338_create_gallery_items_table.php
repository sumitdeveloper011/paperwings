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
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('gallery_id')->constrained('galleries')->cascadeOnDelete();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable()->comment('Path for image files');
            $table->text('video_embed_code')->nullable()->comment('YouTube/Vimeo iframe embed code');
            $table->string('video_url')->nullable()->comment('Direct video URL (MP4, WebM)');
            $table->string('thumbnail_path')->nullable()->comment('Thumbnail for videos or custom thumbnails');
            $table->integer('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->string('alt_text')->nullable();
            $table->timestamps();
            
            $table->index('gallery_id');
            $table->index('type');
            $table->index('order');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
