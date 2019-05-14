<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\TipoTorneio;

class SeedTipoTorneio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tipo_torneio_1 = TipoTorneio::find(1);
        if(!$tipo_torneio_1){
            $tipo_torneio_1 = new TipoTorneio;
            $tipo_torneio_1->id = 1;
        }
        $tipo_torneio_1->name = "Suíço";
        $tipo_torneio_1->save();


        $tipo_torneio_2 = TipoTorneio::find(2);
        if(!$tipo_torneio_2){
            $tipo_torneio_2 = new TipoTorneio;
            $tipo_torneio_2->id = 2;
        }
        $tipo_torneio_2->name = "Schüring";
        $tipo_torneio_2->save();
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
