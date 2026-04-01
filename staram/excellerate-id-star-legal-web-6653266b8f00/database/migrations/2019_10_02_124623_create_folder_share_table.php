<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFolderShareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('folder_share');
        Schema::create('folder_share', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('folder_id', false, true)->nullable();
            $table->foreign('folder_id')->references('id')->on('folder_share');
            $table->string('folder_name',256);
            $table->text('description')->nullable();
            $table->bigInteger('creator_id',false,true);
            $table->foreign('creator_id')->references('id')->on('users');
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
        Schema::dropIfExists('folder_share');
        Schema::enableForeignKeyConstraints();
    }
}
