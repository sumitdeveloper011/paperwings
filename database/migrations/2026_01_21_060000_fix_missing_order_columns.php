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
            // Check and add platform_fee if missing
            if (!Schema::hasColumn('orders', 'platform_fee')) {
                // Try to add after stripe_fee if it exists, otherwise after total
                if (Schema::hasColumn('orders', 'stripe_fee')) {
                    $table->decimal('platform_fee', 10, 2)->nullable()->default(0)->after('stripe_fee')->comment('Platform fee charged to customer');
                } elseif (Schema::hasColumn('orders', 'currency')) {
                    $table->decimal('platform_fee', 10, 2)->nullable()->default(0)->after('currency')->comment('Platform fee charged to customer');
                } else {
                    $table->decimal('platform_fee', 10, 2)->nullable()->default(0)->after('total')->comment('Platform fee charged to customer');
                }
            }
            
            // Check and add stripe_fee if missing
            if (!Schema::hasColumn('orders', 'stripe_fee')) {
                if (Schema::hasColumn('orders', 'currency')) {
                    $table->decimal('stripe_fee', 10, 2)->nullable()->after('currency')->comment('Stripe processing fee charged to customer');
                } else {
                    $table->decimal('stripe_fee', 10, 2)->nullable()->after('total')->comment('Stripe processing fee charged to customer');
                }
            }
            
            // Check and add net_amount if missing
            if (!Schema::hasColumn('orders', 'net_amount')) {
                if (Schema::hasColumn('orders', 'platform_fee')) {
                    $table->decimal('net_amount', 10, 2)->nullable()->after('platform_fee')->comment('Net amount after fees');
                } elseif (Schema::hasColumn('orders', 'stripe_fee')) {
                    $table->decimal('net_amount', 10, 2)->nullable()->after('stripe_fee')->comment('Net amount after fees');
                } else {
                    $table->decimal('net_amount', 10, 2)->nullable()->after('total')->comment('Net amount after fees');
                }
            }
            
            // Check and add stripe_balance_transaction_id if missing
            if (!Schema::hasColumn('orders', 'stripe_balance_transaction_id')) {
                $table->string('stripe_balance_transaction_id')->nullable()->after('stripe_charge_id')->comment('Stripe balance transaction ID for fee retrieval');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns in down() - let the original migrations handle that
        // This migration is just a fix for missing columns
    }
};
