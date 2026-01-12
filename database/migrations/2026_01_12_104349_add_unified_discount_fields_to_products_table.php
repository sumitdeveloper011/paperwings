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
        Schema::table('products', function (Blueprint $table) {
            // Add discount_type (products form already uses this, but not stored in DB)
            $table->enum('discount_type', ['none', 'direct', 'percentage'])
                  ->default('none')
                  ->after('total_price');
            
            // Add discount_value (for percentage: 0-100, for direct: not used)
            $table->decimal('discount_value', 10, 2)
                  ->nullable()
                  ->after('discount_type');
            
            // Add index for discount queries
            $table->index('discount_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['discount_type']);
            $table->dropColumn(['discount_type', 'discount_value']);
        });
    }
};
