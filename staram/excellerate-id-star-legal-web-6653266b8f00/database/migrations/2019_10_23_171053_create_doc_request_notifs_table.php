<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocRequestNotifsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('doc_request_notifs');
        Schema::create('doc_request_notifs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id', false, true);
            $table->foreign('user_id')->references('id')->on('users');
            $table->bigInteger('req_id', false, true);
            $table->foreign('req_id')->references('id')->on('doc_request');
            $table->enum('type',['TYPE_REQUEST','TYPE_REVIEW','TYPE_COMPLETED']);
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
        Schema::dropIfExists('doc_request_notifs');
        Schema::enableForeignKeyConstraints();
    }
}
