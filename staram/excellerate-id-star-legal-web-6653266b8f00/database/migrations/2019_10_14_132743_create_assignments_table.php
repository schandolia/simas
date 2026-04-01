<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('assignments');
        Schema::create('assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('req_id', false, true);
            $table->foreign('req_id')->references('id')->on('doc_request');
            $table->integer('status_id', false, true);
            $table->foreign('status_id')->references('id')->on('rights');
            $table->bigInteger('assignee_id', false, true)->nullable();
            $table->foreign('assignee_id')->references('id')->on('users');
            $table->bigInteger('assigner_id', false, true)->nullable();
            $table->foreign('assigner_id')->references('id')->on('users');
            $table->string('comments')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('assignments');
        Schema::enableForeignKeyConstraints();
    }
}
