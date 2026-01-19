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
            $table->decimal('stripe_fee', 10, 2)->nullable()->after('total')->comment('Stripe processing fee charged to customer');
            $table->decimal('platform_fee', 10, 2)->nullable()->after('stripe_fee')->comment('Platform fee charged to customer');
            $table->decimal('net_amount', 10, 2)->nullable()->after('platform_fee')->comment('Net amount after fees (total - stripe_fee - platform_fee)');
            $table->string('stripe_balance_transaction_id')->nullable()->after('stripe_charge_id')->comment('Stripe balance transaction ID for fee retrieval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_fee',
                'platform_fee',
                'net_amount',
                'stripe_balance_transaction_id'
            ]);
        });
    }
};
