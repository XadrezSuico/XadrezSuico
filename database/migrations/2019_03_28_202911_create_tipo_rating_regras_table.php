<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoRatingRegrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_rating_regras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tipo_ratings_id')->unsigned();
            $table->foreign("tipo_ratings_id")->references("id")->on("tipo_ratings");
            $table->integer('idade_minima')->nullable();
            $table->integer('idade_maxima')->nullable();
            $table->integer('inicial')->default(1900);
            $table->integer('k')->default(30);
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
        Schema::dropIfExists('tipo_rating_regras');
    }
}
