<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountryPurchaseSalesStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_purchase_sales_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('buyer_country');
            $table->string('seller_country');
            $table->string('department');
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
        Schema::dropIfExists('country_purchase_sales_statistics');
    }
}
