<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('seller_id');
            $table->smallInteger('seller_company_id');
            $table->string('department');
            $table->string('country');
            $table->string('county')->nullable();
            $table->string('county_l4')->nullable();
            $table->string('county_l5')->nullable();
            $table->string('county_l6')->nullable();
            $table->json('delivery_days')->nullable();
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
        Schema::dropIfExists('delivery_locations');
    }
}
