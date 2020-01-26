<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoDocumento;
use App\TipoDocumentoPais;
use App\Pais;

class TipoDocumentoPaisController extends Controller
{
    public function getTiposDocumento($pais_id){
        $pais = Pais::find($pais_id);
        if($pais){
            $response = array();
            foreach(TipoDocumentoPais::where([["pais_id","=",$pais->id]])->get() as $tipo_documento_pais){
                $array = array();
                $array["id"] = $tipo_documento_pais->tipo_documento->id;
                $array["name"] = $tipo_documento_pais->tipo_documento->nome;
                $array["is_required"] = $tipo_documento_pais->e_requerido;
                if($tipo_documento_pais->tipo_documento->padrao){
                    $array["pattern"] = $tipo_documento_pais->tipo_documento->padrao;
                }else{
                    $array["pattern"] = false;
                }
                $response[] = $array;
            }
            return response()->json(["ok"=>1,"error"=>0,"data"=>$response]);

        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"O país informado não foi encontrado."]);
    }
}
