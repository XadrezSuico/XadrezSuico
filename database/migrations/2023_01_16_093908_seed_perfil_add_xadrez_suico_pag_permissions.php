<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Perfil;

class SeedPerfilAddXadrezSuicoPagPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        $perfil_11 = Perfil::find(11);
        if(!$perfil_11){
            $perfil_11 = new Perfil;
            $perfil_11->id = 11;
        }
        $perfil_11->name = "XadrezSuíçoPAG - Administrador";
        $perfil_11->save();

        $perfil_12 = Perfil::find(12);
        if(!$perfil_12){
            $perfil_12 = new Perfil;
            $perfil_12->id = 12;
        }
        $perfil_12->name = "XadrezSuíçoPAG - Gerente";
        $perfil_12->save();

        $perfil_13 = Perfil::find(13);
        if(!$perfil_13){
            $perfil_13 = new Perfil;
            $perfil_13->id = 13;
        }
        $perfil_13->name = "XadrezSuíçoPAG - Operador";
        $perfil_13->save();
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
