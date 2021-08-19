<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\TipoTorneio;

class SeedTipoTorneioTableAddChaveSemifinal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tipo_torneio_3 = TipoTorneio::find(3);
        if(!$tipo_torneio_3){
            $tipo_torneio_3 = new TipoTorneio;
            $tipo_torneio_3->id = 3;
        }
        $tipo_torneio_3->name = "Chave Semi-final";
        $tipo_torneio_3->save();
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
