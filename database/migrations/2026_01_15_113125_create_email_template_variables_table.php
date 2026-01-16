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
        Schema::create('email_template_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('email_templates')->cascadeOnDelete();
            $table->string('variable_name');
            $table->text('variable_description')->nullable();
            $table->string('example_value')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();
            
            $table->index('template_id');
            $table->unique(['template_id', 'variable_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_template_variables');
    }
};
