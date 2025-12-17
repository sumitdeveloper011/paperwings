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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer-not-to-say'])->nullable();
            $table->text('bio')->nullable();
            $table->string('image')->nullable()->comment('Profile image');
            $table->date('date_of_birth')->nullable();
            $table->unsignedBigInteger('country')->nullable();
            $table->unsignedBigInteger('region')->nullable();
            $table->unsignedBigInteger('district')->nullable();
            $table->unsignedBigInteger('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Index
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
