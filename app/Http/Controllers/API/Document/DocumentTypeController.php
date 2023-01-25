<?php

namespace App\Http\Controllers\API\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Pais;

class DocumentTypeController extends Controller
{
    public function list($country_id){
        if($country_id){
            if(Pais::where([["id","=",$country_id]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"País não encontrado","httpcode"=>404],404);
            }
            $pais = Pais::where([["id","=",$country_id]])->first();

            $response = array();

            foreach($pais->tipo_documentos->all() as $tipo_documento_pais){
                $response[] = $tipo_documento_pais->toAPIObject();
            }

            return response()->json(["ok"=>1,"error"=>0,"document_types"=>$response]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"País não encontrado","httpcode"=>404],404);
    }
}
