<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTorneioTemplateTableAddTipoTorneio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('torneio_template', function (Blueprint $table) {
            $table->integer('tipo_torneio_id')->unsigned()->default(1)->after("grupo_evento_id");
            $table->foreign('tipo_torneio_id')->references("id")->on("tipo_torneio");
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
