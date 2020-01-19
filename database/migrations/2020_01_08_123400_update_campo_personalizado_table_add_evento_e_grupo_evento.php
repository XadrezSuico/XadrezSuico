<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCampoPersonalizadoTableAddEventoEGrupoEvento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campo_personalizados', function (Blueprint $table) {
            $table->integer("evento_id")->unsigned()->nullable()->after("data_type");
            $table->foreign("evento_id")->references("id")->on("evento");
            $table->integer("grupo_evento_id")->unsigned()->nullable()->after("data_type");
            $table->foreign("grupo_evento_id")->references("id")->on("grupo_evento");
            $table->boolean("is_active")->default(true)->after("data_type");
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
