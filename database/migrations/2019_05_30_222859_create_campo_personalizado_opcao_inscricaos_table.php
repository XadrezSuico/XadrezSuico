<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampoPersonalizadoOpcaoInscricaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campo_personalizado_opcao_inscricaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('campo_personalizados_id')->unsigned();
            $table->foreign('campo_personalizados_id', 'fk_cpoi_cp_1')->references("id")->on("campo_personalizados");
            $table->bigInteger('opcaos_id')->unsigned();
            $table->foreign('opcaos_id', 'fk_cpoi_o_1')->references("id")->on("opcaos");
            $table->integer('inscricao_id')->unsigned();
            $table->foreign('inscricao_id', 'fk_cpoi_i_1')->references("id")->on("inscricao");
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
        Schema::dropIfExists('campo_personalizado_opcao_inscricaos');
    }
}
