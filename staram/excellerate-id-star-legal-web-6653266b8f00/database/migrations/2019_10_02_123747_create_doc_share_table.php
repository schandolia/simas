<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('doc_share');
        Schema::create('doc_share', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('folder_id',false,true)->nullable();
            $table->foreign('folder_id')->references('id')->on('folder_share');
            $table->string('doc_name',256);
            $table->string('company_name', 1024);
            $table->integer('doc_type', false, true);
            $table->foreign('doc_type')->references('id')->on('doc_type');
            $table->date('agreement_date')->nullable();
            $table->string('agreement_number', 1024);
            $table->string('parties', 1024);
            $table->date('expire_date');
            $table->text('remark')->nullable();
            $table->text('description')->nullable();
            $table->string('attachment', 512);
            $table->bigInteger('submitter_id', false, true);
            $table->foreign('submitter_id')->references('id')->on('users');
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
        Schema::dropIfExists('doc_share');
        Schema::enableForeignKeyConstraints();
    }
}
