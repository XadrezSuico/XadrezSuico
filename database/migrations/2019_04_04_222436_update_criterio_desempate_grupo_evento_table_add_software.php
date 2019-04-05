<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCriterioDesempateGrupoEventoTableAddSoftware extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('criterio_desempate_grupo_evento', function (Blueprint $table) {
            $table->bigInteger('softwares_id')->unsigned()->nullable();
            $table->foreign("softwares_id")->references("id")->on("softwares");
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
