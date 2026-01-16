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
            // Currency
            $table->string('currency', 3)->default('NZD')->after('total');
            
            // Payment timestamps
            $table->timestamp('payment_confirmed_at')->nullable()->after('payment_status');
            
            // Stripe additional data
            $table->string('stripe_customer_id')->nullable()->after('stripe_charge_id');
            $table->string('stripe_payment_method_id')->nullable()->after('stripe_customer_id');
            $table->string('stripe_payment_method_type')->nullable()->after('stripe_payment_method_id');
            $table->text('stripe_receipt_url')->nullable()->after('stripe_payment_method_type');
            
            // Refund tracking
            $table->decimal('refund_amount', 10, 2)->nullable()->default(0)->after('stripe_receipt_url');
            $table->text('refund_reason')->nullable()->after('refund_amount');
            $table->timestamp('refunded_at')->nullable()->after('refund_reason');
            
            // Payment failure tracking
            $table->text('payment_failure_reason')->nullable()->after('refunded_at');
            
            // Dispute tracking
            $table->string('dispute_status')->nullable()->after('payment_failure_reason');
            $table->text('dispute_reason')->nullable()->after('dispute_status');
            
            // Indexes
            $table->index('currency');
            $table->index('payment_confirmed_at');
            $table->index('stripe_customer_id');
            $table->index('refunded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['currency']);
            $table->dropIndex(['payment_confirmed_at']);
            $table->dropIndex(['stripe_customer_id']);
            $table->dropIndex(['refunded_at']);
            
            $table->dropColumn([
                'currency',
                'payment_confirmed_at',
                'stripe_customer_id',
                'stripe_payment_method_id',
                'stripe_payment_method_type',
                'stripe_receipt_url',
                'refund_amount',
                'refund_reason',
                'refunded_at',
                'payment_failure_reason',
                'dispute_status',
                'dispute_reason',
            ]);
        });
    }
};
