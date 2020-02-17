<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TipoDocumento;
use App\TipoDocumentoPais;
use App\Enxadrista;
use App\Documento;
use App\Pais;

class DocumentoController extends Controller
{
    public function getDocumento($id, $tipo_documento_id){
        $enxadrista = Enxadrista::find($id);
        if($enxadrista){
            $response = array();
            $documento = Documento::where([["tipo_documentos_id","=",$tipo_documento_id],["enxadrista_id","=",$id]])->first();
            if($documento){
                $array = array();
                $array["id"] = $documento->tipo_documento->id;
                $array["number"] = $documento->numero;
                return response()->json(["ok"=>1,"error"=>0,"data"=>$array]);
            }

        }else{
            return response()->json(["ok"=>0,"error"=>1,"message"=>"O enxadrista informado não foi encontrado."]);
        }
    }
}
