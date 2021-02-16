<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seller_id');
            $table->integer('updated_by_seller_id')->nullable();
            $table->timestamps();
            $table->smallInteger('buyer_company_id');
            $table->smallInteger('seller_company_id');
            $table->integer('delivery_location_id')->nullable();
            $table->json('delivery_days');
            $table->string('payment_frequency');
            $table->string('department');
            $table->boolean('activated_by_seller')->default(0);
            $table->boolean('activated_by_buyer')->default(0);
            $table->boolean('buyer_disabled_language')->default(0);
            $table->boolean('buyer_disabled_currency')->default(0);
            $table->boolean('seller_disabled_language')->default(0);
            $table->boolean('seller_disabled_currency')->default(0);
            $table->json('price_list');
            $table->string('currency');
            $table->string('language');
            $table->string('country');
            $table->string('county')->nullable();
            $table->string('county_l4')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_lists');
    }
}
