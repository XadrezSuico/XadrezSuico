<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Perfil;

class SeedPerfilAddAdministradoresNovos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $perfil_7 = Perfil::find(7);
        if(!$perfil_7){
            $perfil_7 = new Perfil;
            $perfil_7->id = 7;
        }
        $perfil_7->name = "Administrador de Grupo de Evento";
        $perfil_7->save();
        

        $perfil_8 = Perfil::find(8);
        if(!$perfil_8){
            $perfil_8 = new Perfil;
            $perfil_8->id = 8;
        }
        $perfil_8->name = "Coordenador de Cadastro de Cidades e Clubes";
        $perfil_8->save();


        $perfil_9 = Perfil::find(9);
        if(!$perfil_9){
            $perfil_9 = new Perfil;
            $perfil_9->id = 9;
        }
        $perfil_9->name = "Coordenador de Enxadristas";
        $perfil_9->save();
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
