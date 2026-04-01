<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('request_submission');
        Schema::create('request_submission', function (Blueprint $table)
        {
            $table->bigIncrements('id');
            $table->bigInteger('req_id', false, true);
            $table->foreign('req_id')->references('id')->on('doc_request');
            $table->bigInteger('submitter_id', false, true);
            $table->foreign('submitter_id')->references('id')->on('users');
            $table->date('date')->nullable();
            $table->string('agreement_number',512)->nullable();
            $table->string('parties', 1024)->nullable();
            $table->string('transaction_objective', 1024)->nullable();
            $table->string('time_period', 512)->nullable();
            $table->decimal('nominal_transaction',17,2)->nullable();
            $table->string('terms', 1024)->nullable();
            $table->string('other', 1024)->nullable();
            $table->string('attachment_name', 512)->nullable();
            $table->string('attachment_path', 512)->nullable();
            $table->enum('status',['STATE_NOT_DONE','STATE_DONE','STATE_APPROVED','STATE_REJECTED','STATE_TOBE_REVISE'])->default('STATE_NOT_DONE');
            $table->text('notes')->nullable();
            $table->string('version',10);
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
        Schema::dropIfExists('request_submission');
        Schema::enableForeignKeyConstraints();
    }
}
