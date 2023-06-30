<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;
use App\Clube;

class ClubController extends Controller
{
    public function search(Request $request){
        $clubes = Clube::where([
            ["id", "like", "%" . $request->input("q") . "%"],
        ])
        ->orWhere([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])
        ->orderBy("name", "ASC")
        ->limit(30)
        ->get();

        $results = array();
        foreach($clubes as $clube){
            $results[] = $clube->toAPIObject();
        }


        $total = Clube::where([
        ["id", "like", "%" . $request->input("q") . "%"],
        ])
        ->orWhere([
            ["name", "like", "%" . $request->input("q") . "%"],
        ])
        ->orderBy("name", "ASC")
        ->count();


        return response()->json(["ok"=>1, "error"=>0, "clubs" => $results, "result" => ($total > 30) ? true : false]);
    }
    public function get($id,Request $request){
        $count = Clube::where([
            ["id", "=", $id],
        ])->count();

        if($count == 0){
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Clube não encontrado.","httpcode"=>404],404);
        }
        $club = Clube::where([
            ["id", "=", $id],
        ])->first();

        return response()->json(["ok"=>1, "error"=>0, "club" => $club->toAPIObject(true)]);
    }


    public function new(Request $request)
    {
        if (
            !$request->has("name") || !$request->has("city_id")
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Os campos obrigatórios não estão preenchidos. Por favor, verifique e envie novamente!", "registred" => 0]);
        } elseif (
            $request->input("name") == null || $request->input("name") == "" ||
            $request->input("city_id") == null || $request->input("city_id") == ""
        ) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Os campos obrigatórios não estão preenchidos. Por favor, verifique e envie novamente!", "registred" => 0]);
        }
        $clube = new Clube;

        $temClube_count = Clube::where([["name", "=", mb_strtoupper($request->input("name"))], ["cidade_id", "=", $request->input("city_id")]])->count();
        if ($temClube_count > 0) {
            $clube = Clube::where([["name", "=", mb_strtoupper($request->input("name"))], ["cidade_id", "=", $request->input("city_id")]])->first();

            return response()->json(["ok" => 0, "error" => 1, "message" => "Este clube já está cadastrado!",  "club"=>$clube->toAPIObject()]);
        }

        $clube->name = mb_strtoupper($request->input("name"));
        $clube->cidade_id = mb_strtoupper($request->input("city_id"));
        $clube->save();
        if ($clube->id > 0) {
            return response()->json(["ok" => 1, "error" => 0, "club"=>$clube->toAPIObject()]);
        } else {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Um erro inesperado aconteceu. Por favor, tente novamente mais tarde.", "registred" => 0]);
        }
    }


    public function searchList(Request $request)
    {
        if($request->has("is_fexpar___clube_valido_vinculo_federativo")){
            $clubes = Clube::where([["is_fexpar___clube_valido_vinculo_federativo","=",true]])
            ->where(function($q1) use ($request) {
                $q1->where([
                    ["name", "like", "%" . $request->input("q") . "%"],
                ])->orWhere(function ($q) use ($request) {
                    $q->whereHas("cidade", function ($Q) use ($request) {
                        $Q->where([
                            ["name", "like", "%" . $request->input("q") . "%"],
                        ]);
                    });
                });
            })
            ->limit(30)
            ->get();
        }else{
            $clubes = Clube::where([
                ["name", "like", "%" . $request->input("q") . "%"],
            ])->orWhere(function ($q) use ($request) {
                $q->whereHas("cidade", function ($Q) use ($request) {
                    $Q->where([
                        ["name", "like", "%" . $request->input("q") . "%"],
                    ]);
                });
            })
            ->limit(30)
            ->get();
        }
        $results = array();
        if(!$request->has("is_fexpar___clube_valido_vinculo_federativo")) $results[] = array("id" => -1, "text" => "Sem Clube");
        foreach ($clubes as $clube) {
            $results[] = array("id" => $clube->id, "text" => $clube->getFullName());
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }
}
