<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCategoriaTableAddEventoEGrupoDeEvento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categoria', function (Blueprint $table) {
            $table->integer("evento_id")->unsigned()->nullable()->after("code");
            $table->foreign("evento_id")->references("id")->on("evento");
            $table->integer("grupo_evento_id")->unsigned()->nullable()->after("code");
            $table->foreign("grupo_evento_id")->references("id")->on("grupo_evento");
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
