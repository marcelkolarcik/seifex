<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseSalesStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_sales_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('buyer_company_id');
            $table->integer('seller_company_id');
            $table->string('department');
            $table->json('product_list');
            $table->float('order_value');
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
        Schema::dropIfExists('purchase_sales_statistics');
    }
}
