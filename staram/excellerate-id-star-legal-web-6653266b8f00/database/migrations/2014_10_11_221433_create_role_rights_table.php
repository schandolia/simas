<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('role_rights');
        Schema::create('role_rights', function (Blueprint $table) {
            $table->integer('role_id', false, true);
            $table->foreign('role_id')->references('id')->on('roles');
            $table->integer('right_id', false, true);
            $table->foreign('right_id')->references('id')->on('rights');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('role_rights');
        Schema::enableForeignKeyConstraints();
    }
}
