<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffDutiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::create('staff_duties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('admin_id')->nullable();
            $table->integer('owner_id')->nullable();
            $table->string('role');
            $table->string('duty_name');
            $table->string('duty_for');
            $table->boolean('lead_duty');
            $table->text('duty_description');
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
        Schema::dropIfExists('staff_duties');
    }
}
