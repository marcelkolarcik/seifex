<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeifexOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seifex_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('seller_company_id');
            $table->smallInteger('buyer_company_id');
            $table->string('department');
            $table->string('buyer_country');
            $table->string('buyer_county')->nullable();
            $table->string('buyer_county_l4')->nullable();
            $table->string('seller_country');
            $table->string('seller_county')->nullable();
            $table->string('seller_county_l4')->nullable();
            $table->json('order');
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
        Schema::dropIfExists('seifex_orders');
    }
}
