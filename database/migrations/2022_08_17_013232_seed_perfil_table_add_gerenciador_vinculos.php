<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Perfil;

class SeedPerfilTableAddGerenciadorVinculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(env("ENTITY_DOMAIN",null) == "fexpar.com.br"){
            $perfil = new Perfil;
            $perfil->id = 10;
            $perfil->name = "FEXPAR - Gestor de Vínculos Federativos";
            $perfil->save();
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
