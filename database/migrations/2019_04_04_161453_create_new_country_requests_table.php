<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewCountryRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_country_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('seifex_country_id');
            $table->string('country_name');
            $table->string('requester_email')->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('token');
            
            $table->timestamps();
        });
        DB::statement('ALTER TABLE `new_country_requests` ADD `ip_address` VARBINARY(16)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_country_requests');
    }
}
