<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('buyer_id');
            $table->integer('seller_id');
            $table->integer('delivery_location_id');
            $table->integer('seller_company_id');
            $table->integer('buyer_company_id');
            $table->string('seller_company_name');
            $table->string('buyer_company_name');
            $table->string('department');
            $table->float('total_order_cost');
            $table->json('order');
            $table->text('comment')->nullable();
            $table->text('not_available');
            $table->timestamp('checked_at')->nullable();
            $table->timestamp('prepped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('buyer_confirmed_delivery_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('invoiced_at')->nullable();
            $table->string('invoice_freq')->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
