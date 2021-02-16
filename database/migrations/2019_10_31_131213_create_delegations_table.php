<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelegationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delegations', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('delegator_id');
            $table->smallInteger('delegator_company_id');
            $table->string('delegator_company_name');
            $table->string('delegator_role');
            $table->string('delegator_email');
            $table->string('token');
            $table->string('staff_role');
            $table->string('staff_position');
            $table->string('staff_email');
            $table->string('staff_name');
            $table->string('staff_id')->nullable();
            $table->timestamp('delegated_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('undelegated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delegations');
    }
}
