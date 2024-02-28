<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Perfil;

class SeedPerfilAddEventClassificateRuleOperator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $perfil = Perfil::find(16);
        if (!$perfil) {
            $perfil = new Perfil;
            $perfil->id = 16;
        }
        $perfil->name = "XadrezSuíço Classificador - Operador (Regras)";
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
