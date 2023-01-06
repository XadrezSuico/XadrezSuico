<?php

namespace App\Http\Controllers\API\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;
use App\Clube;

class ClubController extends Controller
{
    public function search($uuid,Request $request){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();


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
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }
    public function get($uuid,$id,Request $request){
        if($uuid){
            if(Evento::where([["uuid","=",$uuid]])->count() == 0){
                return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado.","httpcode"=>404],404);
            }
            $evento = Evento::where([["uuid","=",$uuid]])->first();


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
        return response()->json(["ok"=>0,"error"=>1,"message"=>"Evento não encontrado","httpcode"=>404],404);
    }
}
