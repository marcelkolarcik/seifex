<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminDelegationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_delegations', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('delegator_id');
            $table->string('delegator_role');
            $table->string('delegator_email');
            $table->string('token');
            $table->string('delegated_role');
            $table->string('delegated_email');
            $table->string('delegated_name');
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
        Schema::dropIfExists('admin_delegations');
    }
}
