<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\TipoDocumento;
use App\TipoDocumentoPais;
use App\Pais;

class SeedTipoDocumentoPais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tipo_documento_pais_1 = new TipoDocumentoPais;
        $tipo_documento_pais_1->tipo_documentos_id = 1;
        $tipo_documento_pais_1->pais_id = 33;
        $tipo_documento_pais_1->save();

        $tipo_documento_pais_2 = new TipoDocumentoPais;
        $tipo_documento_pais_2->tipo_documentos_id = 2;
        $tipo_documento_pais_2->pais_id = 33;
        $tipo_documento_pais_2->save();

        $tipo_documento_pais_3 = new TipoDocumentoPais;
        $tipo_documento_pais_3->tipo_documentos_id = 3;
        $tipo_documento_pais_3->pais_id = 33;
        $tipo_documento_pais_3->save();

        foreach(array(3,4) as $tipo_documento){
            foreach(Pais::where([["id","!=",33]])->get() as $pais){
                $tipo_documento_pais_temporary = new TipoDocumentoPais;
                $tipo_documento_pais_temporary->tipo_documentos_id = $tipo_documento;
                $tipo_documento_pais_temporary->pais_id = $pais->id;
                $tipo_documento_pais_temporary->save();
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
        //
    }
}
