<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Software;
use App\Torneio;

class UpdateFieldsSoftwareFromTorneioWhenNeedToBeLichessOrXadrezSuico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $xadrezsuico = Software::where([["name","=","XadrezSuíço"]])->first();
        $lichess = Software::where([["name","=","Lichess.org (Online)"]])->first();

        foreach(Torneio::all() as $torneio){
            if($torneio->evento->is_lichess_integration){ // Se usa integração com o Lichess.org, ele é operado pelo Lichess
                $torneio->softwares_id = $lichess->id;
                $torneio->save();
            }elseif($torneio->tipo_torneio->name == "Chave Semi-final"){ // Se o tipo for de chave, o Software é o XadrezSuíço
                $torneio->softwares_id = $xadrezsuico->id;
                $torneio->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('torneio_when_need_to_be_lichess_or_xadrez_suico', function (Blueprint $table) {
            //
        });
    }
}
