<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoRatingEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_rating_eventos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('tipo_ratings_id')->unsigned();
            $table->foreign("tipo_ratings_id")->references("id")->on("tipo_ratings");
            $table->integer('evento_id')->unsigned();
            $table->foreign("evento_id")->references("id")->on("evento");
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
        Schema::dropIfExists('tipo_rating_eventos');
    }
}
