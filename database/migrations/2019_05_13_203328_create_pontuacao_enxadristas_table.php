<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePontuacaoEnxadristasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pontuacao_enxadristas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('enxadrista_id')->unsigned();
            $table->foreign('enxadrista_id')->references("id")->on("enxadrista");
            $table->integer('evento_id')->unsigned()->nullable();
            $table->foreign('evento_id')->references("id")->on("evento");
            $table->integer('grupo_evento_id')->unsigned()->nullable();
            $table->foreign('grupo_evento_id')->references("id")->on("grupo_evento");
            $table->integer('categoria_id')->unsigned()->nullable();
            $table->foreign('categoria_id')->references("id")->on("categoria");
            $table->integer('pontos');
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
        Schema::dropIfExists('pontuacao_enxadristas');
    }
}
