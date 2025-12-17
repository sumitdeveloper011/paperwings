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
        Schema::table('orders', function (Blueprint $table) {
            // Add indexes for fast search
            $table->index('billing_email');
            $table->index('billing_first_name');
            $table->index('billing_last_name');
            $table->index('created_at');

            // Composite indexes for common search patterns
            $table->index(['status', 'created_at']);
            $table->index(['payment_status', 'created_at']);
            $table->index(['status', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['billing_email']);
            $table->dropIndex(['billing_first_name']);
            $table->dropIndex(['billing_last_name']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['payment_status', 'created_at']);
            $table->dropIndex(['status', 'payment_status']);
        });
    }
};

