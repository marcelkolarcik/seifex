<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyerCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        
            $table->integer('buyer_id');
        
            $table->json('currencies');
            $table->json('languages');
        
            $table->string('buyer_owner_name');
            $table->string('buyer_owner_email');
            $table->string('buyer_owner_phone_number');
        
            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('buyer_phone_number');
        
            $table->string('buyer_accountant_name');
            $table->string('buyer_accountant_email');
            $table->string('buyer_accountant_phone_number');
        
            $table->string('buyer_company_name');
            $table->string('address');
            $table->string('VAT_number');
        
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
        Schema::dropIfExists('buyer_companies');
    }
}
