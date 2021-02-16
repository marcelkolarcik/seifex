<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceListsExtendedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    
        Schema::create('price_lists_extended', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seller_id');
            $table->integer('updated_by_seller_id')->nullable();
            $table->integer('seller_company_id');
            $table->string('department');
            $table->timestamps();
            $table->json('price_list');
            $table->softDeletes();
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_lists_extended');
    }
}
