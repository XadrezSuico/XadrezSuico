<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTournamentConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('torneio_id')->unsigned();
            $table->foreign('torneio_id')->references('id')->on("torneio");
            $table->string('key');

            $table->enum('value_type',["integer","float","decimal","boolean","string"]);

            $table->integer("integer")->nullable();
            $table->float("float")->nullable();
            $table->decimal("decimal",9,2)->nullable();
            $table->boolean("boolean")->nullable();
            $table->string("string")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournament_configs');
    }
}
