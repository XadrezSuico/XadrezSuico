<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampoPersonalizadoGrupoEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campo_personalizado_grupo_eventos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('grupo_evento_id')->unsigned();
            $table->foreign('grupo_evento_id', 'fk_cpge_ge_1')->references("id")->on("grupo_evento");
            $table->bigInteger('campo_personalizados_id')->unsigned();
            $table->foreign('campo_personalizados_id', 'fk_cpge_cp_1')->references("id")->on("campo_personalizados");
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
        Schema::dropIfExists('campo_personalizado_grupo_eventos');
    }
}
