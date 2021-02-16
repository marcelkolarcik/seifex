<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkScopesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_scopes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('delegation_id');
            $table->integer('staff_id')->nullable();
            $table->string('staff_phone_number');
            $table->string('guard');
            $table->json('details');
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
        Schema::dropIfExists('work_scopes');
    }
}
