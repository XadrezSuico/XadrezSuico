<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTemplateTorneioTableAddGrupoEvento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('torneio_template', function (Blueprint $table) {
            $table->integer("grupo_evento_id")->unsigned()->nullable()->after("name");
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
