<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Perfil;

class SeedPerfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $perfil_1 = Perfil::find(1);
        if(!$perfil_1){
            $perfil_1 = new Perfil;
            $perfil_1->id = 1;
        }
        $perfil_1->name = "Super-Administrador";
        $perfil_1->save();


        $perfil_2 = Perfil::find(2);
        if(!$perfil_2){
            $perfil_2 = new Perfil;
            $perfil_2->id = 2;
        }
        $perfil_2->name = "Administrador";
        $perfil_2->save();


        $perfil_3 = Perfil::find(3);
        if(!$perfil_3){
            $perfil_3 = new Perfil;
            $perfil_3->id = 3;
        }
        $perfil_3->name = "Diretor de Torneio";
        $perfil_3->save();


        $perfil_4 = Perfil::find(4);
        if(!$perfil_4){
            $perfil_4 = new Perfil;
            $perfil_4->id = 4;
        }
        $perfil_4->name = "Árbitro Mesa";
        $perfil_4->save();


        $perfil_5 = Perfil::find(5);
        if(!$perfil_5){
            $perfil_5 = new Perfil;
            $perfil_5->id = 5;
        }
        $perfil_5->name = "Árbitro de Confirmação";
        $perfil_5->save();


        $perfil_6 = Perfil::find(6);
        if(!$perfil_6){
            $perfil_6 = new Perfil;
            $perfil_6->id = 6;
        }
        $perfil_6->name = "Diretor de Grupo de Torneio";
        $perfil_6->save();
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
