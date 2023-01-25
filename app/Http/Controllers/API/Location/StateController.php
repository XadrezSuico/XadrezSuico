<?php

namespace App\Http\Controllers\API\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Estado;
use App\Pais;

class StateController extends Controller
{
    public function list($country_id){
        $states = Estado::where([["pais_id","=",$country_id]])->orderBy("nome","ASC")->get();
        $return = array();

        foreach($states as $state){
            $return[] = $state->toAPIObject();
        }

        return response()->json(["ok"=>1,"error"=>0,"states"=>$return]);
    }

    public function get($state_id){
        if(Estado::where([["id","=",$state_id]])->count() > 0){
            $state = Estado::find($state_id);

            return response()->json(["ok"=>1,"error"=>0,"state"=>$state->toAPIObject(true)]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Estado não encontrado."]);
    }

    public function new(Request $request)
    {
        if (
            !$request->has("name") || !$request->has("country_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!"]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("country_id") == null || $request->input("country_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!"]);
        }
        $pais = Pais::find($request->input("country_id"));
        if(!$pais){
            return response()->json(["ok" => 0, "error" => 1, "message" => "O país não foi encontrado. Por favor, verifique e tente novamente!"]);
        }
        if($pais->id == 33){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este país não permite o cadastro de estado."]);
        }

        $estado = new Estado;

        $estado_count = Estado::where([["nome", "=", mb_strtoupper($request->input("name"))],["pais_id","=",$request->input("country_id")]])->count();
        if ($estado_count > 0) {
            $estado = Estado::where([["nome", "=", mb_strtoupper($request->input("name"))],["pais_id","=",$request->input("country_id")]])->first();
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este estado já está cadastrado! Selecionamos ele para você.", "state" => $estado->toAPIObject()]);
        }

        $estado->nome = mb_strtoupper($request->input("name"));
        $estado->pais_id = mb_strtoupper($request->input("country_id"));
        $estado->save();

        if ($estado->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "state" => $estado->toAPIObject()]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde."]);
        }
    }
}
