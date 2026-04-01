<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('doc_request');
        Schema::create('doc_request', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('doc_type',false,true);
            $table->foreign('doc_type')->references('id')->on('doc_type');
            $table->enum('approval_type',['REQUEST', 'REVIEW'])->default('REQUEST');
            $table->string('proposed_by', 512)->nullable();
            $table->date('proposed_date')->nullable();
            $table->text('purpose')->nullable();
            $table->string('parties', 1024)->nullable();
            $table->text('description')->nullable();
            $table->string('commercial_terms', 512)->nullable();
            $table->decimal('transaction_value', 17, 2)->nullable();
            $table->string('late_payment_toleration', 512)->nullable();
            $table->string('condition_precedent', 512)->nullable();
            $table->string('termination_terms', 512)->nullable();
            $table->string('payment_terms', 512)->nullable();
            $table->string('delay_penalty', 512)->nullable();
            $table->string('guarantee', 512)->nullable();
            $table->string('agreement_terms', 512)->nullable();
            $table->boolean('isActive')->default(true);
            $table->integer('status', false, true)->default(1);
            $table->foreign('status')->references('id')->on('rights');
            $table->integer('nextStatus', false, true)->nullable();
            $table->foreign('nextStatus')->references('id')->on('rights');
            $table->bigInteger('owner_id', false, true)->nullable();
            $table->foreign('owner_id')->references('id')->on('users');
            $table->bigInteger('last_owner_id', false, true)->nullable();
            $table->foreign('last_owner_id')->references('id')->on('users');
            $table->bigInteger('requester_id', false, true);
            $table->foreign('requester_id')->references('id')->on('users');
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
        Schema::dropIfExists('doc_request');
        Schema::enableForeignKeyConstraints();
    }
}
