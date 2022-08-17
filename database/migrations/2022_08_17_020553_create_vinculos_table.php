<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVinculosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vinculos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid')->unique();
            $table->integer('enxadrista_id')->unsigned();
            $table->foreign('enxadrista_id')->references("id")->on("enxadrista");

            $table->year('ano');

            $table->integer('cidade_id')->unsigned();
            $table->foreign('cidade_id')->references("id")->on("cidade");
            $table->integer('clube_id')->unsigned();
            $table->foreign('clube_id')->references("id")->on("clube");

            $table->boolean("is_confirmed_system")->default(false);
            $table->boolean("is_confirmed_manually")->default(false);

            $table->integer("system_inscricoes_in_this_club_confirmed")->default(0);

            $table->text("events_played")->nullable();

            $table->text("obs")->nullable();

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
        Schema::dropIfExists('vinculos');
    }
}
