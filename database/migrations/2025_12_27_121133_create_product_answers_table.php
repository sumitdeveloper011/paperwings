<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_answers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('question_id')->constrained('product_questions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name')->nullable();
            $table->text('answer');
            $table->integer('helpful_count')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0=Rejected, 1=Approved');
            $table->timestamps();
            
            $table->index('question_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_answers');
    }
};
