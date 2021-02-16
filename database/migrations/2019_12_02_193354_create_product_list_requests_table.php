<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductListRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_list_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seller_id')->nullable();
            $table->timestamps();
            $table->integer('seller_company_id');
            $table->integer('buyer_company_id');
            $table->integer('delivery_location_id')->nullable();
            $table->integer('responder_user_id')->nullable();
            $table->integer('requester_user_id')->nullable();
            $table->string('department');
            $table->boolean('requested')->default(0);
            $table->boolean('responded')->default(0);
            $table->string('requester')->default(0);
            $table->string('guard')->default(0);
            $table->boolean('dismissed')->default(0);
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
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
        Schema::dropIfExists('product_list_requests');
    }
}
