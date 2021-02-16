<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
        
            $table->integer('seller_company_id');
            $table->integer('buyer_company_id');
            $table->string('period');
            $table->json('order_ids');
            $table->string('department')->nullable();
            $table->string('invoice_freq');
            $table->float('invoice_cost');
        
            $table->integer('sender_user_id');
            $table->timestamp('confirmed_at')->nullable();
            $table->integer('confirmed_by_user_id')->nullable();
        
            $table->timestamp('paid_at')->nullable();
            $table->integer('payer_user_id')->nullable();
        
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
        Schema::dropIfExists('invoices');
    }
}
