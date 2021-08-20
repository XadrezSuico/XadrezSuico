<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\CriterioDesempate;


class SeedCriterioDesempateAddLichessTiebreak extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $criterio = new CriterioDesempate;
        $criterio->name = "Critério de Desempate do Lichess.org";
        $criterio->code = "Lcs";
        $criterio->is_lichess = true;
        $criterio->save();

        $criterio_1 = new CriterioDesempate;
        $criterio_1->name = "Desempate Manual";
        $criterio_1->code = "XSMn";
        $criterio_1->is_manual = true;
        $criterio_1->is_internal = true;
        $criterio_1->save();
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
