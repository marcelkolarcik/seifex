<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductListUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_list_updates', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->smallInteger('buyer_company_id');
            $table->smallInteger('seller_company_id');
            $table->string('department');
            $table->string('seller_updated')->default('no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_list_updates');
    }
}
