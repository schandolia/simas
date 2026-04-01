<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('attachments');
        Schema::create('attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('req_id', false, true);
            $table->foreign('req_id')->references('id')->on('doc_request');
            $table->enum('kind',['KIND_AKTA','KIND_NPWP','KIND_TDP','KIND_KTP','KIND_PROPOSAL','KIND_OTHER'])->default('KIND_OTHER');
            $table->string('filename',256);
            $table->string('path', 512);
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
        Schema::dropIfExists('attachments');
        Schema::enableForeignKeyConstraints();
    }
}
