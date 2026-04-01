<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocApprovalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('doc_approval');
        Schema::create('doc_approval', function (Blueprint $table) {
            $table->bigInteger('req_id', false, true);
            $table->foreign('req_id')->references('id')->on('doc_request');
            $table->boolean('ceo_approved')->nullable();
            $table->boolean('cfo_approved')->nullable();
            $table->boolean('bu_approved')->nullable();
            $table->boolean('legal_approved')->nullable();
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
        Schema::dropIfExists('doc_approval');
        Schema::enableForeignKeyConstraints();
    }
}
