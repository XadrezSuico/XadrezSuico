<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Perfil;

class SeedPerfilAddEventClassificateAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $perfil = Perfil::find(14);
        if (!$perfil) {
            $perfil = new Perfil;
            $perfil->id = 14;
        }
        $perfil->name = "XadrezSuíço Classificador - Admin";
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
