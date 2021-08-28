<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\CriterioDesempate;

class SeedCriterioDesempateAddTipoTorneio3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $criterio = new CriterioDesempate;
        $criterio->name = "Resultado do Emparceiramento Final";
        $criterio->code = "RF";
        $criterio->internal_code = "TT3_1";
        $criterio->is_internal = true;
        $criterio->is_lichess = true;
        $criterio->save();

        $criterio = new CriterioDesempate;
        $criterio->name = "Definição de 3o Lugar (Perdedor durante a semi-final do vencedor do torneio)";
        $criterio->code = "D3oL";
        $criterio->internal_code = "TT3_2";
        $criterio->is_internal = true;
        $criterio->is_lichess = true;
        $criterio->save();
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
