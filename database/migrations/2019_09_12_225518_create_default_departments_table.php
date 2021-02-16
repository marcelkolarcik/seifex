<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('default_departments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->smallInteger('admin_id')->nullable();
            $table->smallInteger('owner_id')->nullable();
            $table->string('department')->unique();
            $table->boolean('deleted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('default_departments');
    }
}
