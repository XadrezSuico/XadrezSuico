<?php

namespace App\Http\Controllers\API\Event;

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
}
