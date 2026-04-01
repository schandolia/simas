<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('doc_type');
        Schema::create('doc_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 400);
            $table->text('description');
            $table->json('approval_path');
            $table->integer('sla_min', false, true);
            $table->integer('sla_max', false, true);
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
        Schema::dropIfExists('doc_type');
        Schema::enableForeignKeyConstraints();
    }
}
