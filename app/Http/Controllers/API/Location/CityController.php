<?php

namespace App\Http\Controllers\API\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Estado;
use App\Cidade;

class CityController extends Controller
{
    public function list($state_id){
        $cities = Cidade::where([["estados_id","=",$state_id]])->orderBy("name","ASC")->get();
        $return = array();

        foreach($cities as $city){
            $return[] = $city->toAPIObject();
        }

        return response()->json(["ok"=>1,"error"=>0,"cities"=>$return]);
    }

    public function get($state_id){
        if(Cidade::where([["id","=",$state_id]])->count() > 0){
            $city = Cidade::find($state_id);

            return response()->json(["ok"=>1,"error"=>0,"city"=>$city->toAPIObject(true)]);
        }
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Cidade não encontrada."]);
    }


    public function new(Request $request)
    {
        if (
            !$request->has("name") || !$request->has("state_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!"]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("state_id") == null || $request->input("state_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um dos campos obrigatórios não está preenchido. Por favor, verifique e envie novamente!"]);
        }
        $estado = Estado::find($request->input("state_id"));
        if(!$estado){
            return response()->json(["ok" => 0, "error" => 1, "message" => "O estado não foi encontrado. Por favor, verifique e tente novamente!"]);
        }
        if($estado->pais_id == 33){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Este país não permite o cadastro de cidade."]);
        }

        $cidade = new Cidade;

        $cidade_count = Cidade::where([["name", "=", mb_strtoupper($request->input("name"))],["estados_id","=",$request->input("state_id")]])->count();
        if ($cidade_count > 0) {
            $cidade = Cidade::where([["name", "=", mb_strtoupper($request->input("name"))],["estados_id","=",$request->input("state_id")]])->first();
            return response()->json(["ok" => 0, "error" => 1, "message" => "Esta cidade já está cadastrada!", "city" => $cidade->toAPIObject(true)]);
        }

        $cidade->name = mb_strtoupper($request->input("name"));
        $cidade->estados_id = mb_strtoupper($request->input("state_id"));
        $cidade->save();
        if ($cidade->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "city" => $cidade->toAPIObject(true)]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde."]);
        }
    }
}
