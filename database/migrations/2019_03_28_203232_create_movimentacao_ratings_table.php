<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimentacaoRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimentacao_ratings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('tipo_ratings_id')->unsigned();
            $table->foreign("tipo_ratings_id")->references("id")->on("tipo_ratings");
            $table->bigInteger('ratings_id')->unsigned();
            $table->foreign("ratings_id")->references("id")->on("ratings");
            $table->integer('torneio_id')->unsigned();
            $table->foreign("torneio_id")->references("id")->on("torneio");
            $table->integer('valor');
            $table->boolean('is_inicial');
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
        Schema::dropIfExists('movimentacao_ratings');
    }
}
