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
        Schema::table('cart_items', function (Blueprint $table) {
            // Drop composite index that includes session_id
            $table->dropIndex(['session_id', 'product_id']);
            
            // Drop session_id index
            $table->dropIndex(['session_id']);
            
            // Drop session_id column
            $table->dropColumn('session_id');
            
            // Make user_id NOT NULL (remove nullable constraint)
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Make user_id nullable again
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add session_id column back
            $table->string('session_id')->nullable()->comment('For guest users')->after('user_id');
            
            // Re-add indexes
            $table->index('session_id');
            $table->index(['session_id', 'product_id']);
        });
    }
};
