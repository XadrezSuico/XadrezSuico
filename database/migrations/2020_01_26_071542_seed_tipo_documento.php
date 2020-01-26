<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\TipoDocumento;

class SeedTipoDocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tipo_documento_1 = new TipoDocumento;
        $tipo_documento_1->id = 1;
        $tipo_documento_1->nome = "CPF";
        $tipo_documento_1->validacao = "cpf";
        $tipo_documento_1->padrao = "000.000.000-00";
        $tipo_documento_1->save();
        
        $tipo_documento_2 = new TipoDocumento;
        $tipo_documento_2->id = 2;
        $tipo_documento_2->nome = "RG";
        $tipo_documento_2->save();
        
        $tipo_documento_3 = new TipoDocumento;
        $tipo_documento_3->id = 3;
        $tipo_documento_3->nome = "Passaporte";
        $tipo_documento_3->save();
        
        $tipo_documento_4 = new TipoDocumento;
        $tipo_documento_4->id = 4;
        $tipo_documento_4->nome = "Identidade";
        $tipo_documento_4->save();

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
