<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEventoTableAddGrupoEventoClassificador extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evento', function (Blueprint $table) {
            $table->integer('grupo_evento_classificador_id')->unsigned()->nullable()->after("evento_classificador_id");
            $table->foreign('grupo_evento_classificador_id')->references("id")->on("grupo_evento");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
