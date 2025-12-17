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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id')->nullable();

            // Billing information
            $table->string('billing_first_name');
            $table->string('billing_last_name');
            $table->string('billing_email');
            $table->string('billing_phone');
            $table->string('billing_street_address');
            $table->string('billing_city');
            $table->string('billing_suburb')->nullable();
            $table->unsignedBigInteger('billing_region_id')->nullable();
            $table->string('billing_zip_code');
            $table->string('billing_country')->default('New Zealand');

            // Shipping information
            $table->string('shipping_first_name');
            $table->string('shipping_last_name');
            $table->string('shipping_email');
            $table->string('shipping_phone');
            $table->string('shipping_street_address');
            $table->string('shipping_city');
            $table->string('shipping_suburb')->nullable();
            $table->unsignedBigInteger('shipping_region_id')->nullable();
            $table->string('shipping_zip_code');
            $table->string('shipping_country')->default('New Zealand');

            // Order totals
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Payment information
            $table->string('payment_method')->default('stripe');
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_charge_id')->nullable();

            // Order status
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('billing_region_id')->references('id')->on('regions')->onDelete('set null');
            $table->foreign('shipping_region_id')->references('id')->on('regions')->onDelete('set null');
            $table->index('order_number');
            $table->index('user_id');
            $table->index('payment_status');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
