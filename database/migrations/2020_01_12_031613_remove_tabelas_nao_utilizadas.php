<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTabelasNaoUtilizadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists("campo_personalizado_grupo_eventos");
        Schema::dropIfExists("categoria_grupo_evento");
        Schema::dropIfExists("torneio_template_grupo_evento");
        Schema::dropIfExists("user_perfils");
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
