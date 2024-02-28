<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Perfil;

class SeedPerfilAddEventClassificateOperator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $perfil = Perfil::find(15);
        if (!$perfil) {
            $perfil = new Perfil;
            $perfil->id = 15;
        }
        $perfil->name = "XadrezSuíço Classificador - Operador";
        $perfil->save();
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
