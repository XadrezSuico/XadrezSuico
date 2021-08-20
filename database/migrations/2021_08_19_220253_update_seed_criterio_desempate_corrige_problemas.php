<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\CriterioDesempate;

class UpdateSeedCriterioDesempateCorrigeProblemas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $criterios = CriterioDesempate::where([["is_geral","=",true]])->get();
        foreach($criterios as $criterio){
            $criterio->is_sm = false;
            $criterio->save();
        }
        $criterios = CriterioDesempate::where([["is_manual","=",true]])->get();
        foreach($criterios as $criterio){
            $criterio->is_sm = false;
            $criterio->save();
        }
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
