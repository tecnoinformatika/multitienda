<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('document')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('canal_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // WooCommerce o MercadoLibre
            $table->string('platform_order_id')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('customers_id')->constrained()->onDelete('cascade');
            $table->string('canal_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->decimal('total_paid_amount', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();
        });

        Schema::create('order_shipping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('shipping_method')->nullable();
            $table->string('shipping_status')->nullable();
            $table->dateTime('shipping_date')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postcode')->nullable();
            $table->string('shipping_country')->nullable();
            $table->timestamps();
        });

        Schema::create('order_billing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postcode')->nullable();
            $table->string('billing_country')->nullable();
            $table->timestamps();
        });

    }

    public function down()
    {

        Schema::dropIfExists('order_billing');
        Schema::dropIfExists('order_shipping');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('customers');

    }
};
