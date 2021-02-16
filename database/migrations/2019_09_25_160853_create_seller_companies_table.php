<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellerCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_companies', function ( Blueprint $table ) {
            $table->bigIncrements('id');
            $table->timestamps();
        
            $table->integer('seller_id');
        
            $table->json('currencies');
            $table->json('languages');
        
            $table->string('seller_owner_name');
            $table->string('seller_owner_email');
            $table->string('seller_owner_phone_number');
        
            $table->string('seller_name');
            $table->string('seller_email');
            $table->string('seller_phone_number');
        
            $table->string('seller_accountant_name');
            $table->string('seller_accountant_email');
            $table->string('seller_accountant_phone_number');
        
            $table->string('seller_delivery_name');
            $table->string('seller_delivery_email');
            $table->string('seller_delivery_phone_number');
        
            $table->string('seller_company_name');
            $table->string('address');
            $table->string('VAT_number');
        
            $table->string('last_order_at');
            $table->string('delivery_days');
        
            $table->string('payment_method');
        
            $table->string('country');
            $table->string('county')->nullable();
            $table->string('county_l4')->nullable();
            $table->string('county_l5')->nullable();
            $table->string('county_l6')->nullable();
        
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_companies');
    }
}
