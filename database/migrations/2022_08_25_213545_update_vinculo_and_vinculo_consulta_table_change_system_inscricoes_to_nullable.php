<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVinculoAndVinculoConsultaTableChangeSystemInscricoesToNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vinculos', function (Blueprint $table) {
            $table->integer("system_inscricoes_in_this_club_confirmed")->nullable()->change();
        });
        Schema::table('vinculo_consultas', function (Blueprint $table) {
            $table->integer("system_inscricoes_in_this_club_confirmed")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nullable', function (Blueprint $table) {
            //
        });
    }
}
