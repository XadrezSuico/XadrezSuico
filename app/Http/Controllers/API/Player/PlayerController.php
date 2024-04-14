<?php

namespace App\Http\Controllers\API\Player;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Enxadrista;

class PlayerController extends Controller
{
    public function list(Request $request){
        $enxadristas = Enxadrista::where(function ($q1) use ($request) {
            $q1->where([
                ["id", "like", "%" . $request->input("q") . "%"],
            ])
            ->orWhere([
                ["name", "like", "%" . $request->input("q") . "%"],
            ])
            ->orWhere(function ($q1) use ($request) {
                $q1->whereHas("documentos", function ($q2) use ($request) {
                    $q2->where([
                        ["numero", "=", $request->input("q")]
                    ]);
                });
            });
        })
        ->whereDoesntHave("configs", function ($q1) {
            $q1->where([["key", "=", "united_to"]]);
        })->orderBy("name", "ASC")->limit(30)->get();

        $results = array();
        foreach($enxadristas as $enxadrista){
            $item = array();
            $item["id"] = $enxadrista->id;
            $item["name"] = $enxadrista->name;
            if ($enxadrista->titles()->count() > 0) {
                $title = $enxadrista->getTitle();
                $item["name"] = "[{$title->title->abbr}] ".$enxadrista->name;
            }
            $item["birthday"] = $enxadrista->getNascimentoPublico();
            $item["fide_id"] = ($enxadrista->fide_id) ? intval($enxadrista->fide_id) : null;
            $item["cbx_id"] = ($enxadrista->cbx_id) ? intval($enxadrista->cbx_id) : null;
            $item["lbx_id"] = ($enxadrista->cbx_id) ? intval($enxadrista->lbx_id) : null;
            $item["chesscom_username"] = $enxadrista->chess_com_username;
            $item["lichess_username"] = $enxadrista->lichess_username;
            $item["city_name"] = $enxadrista->cidade->getName();
            $item["club_name"] = ($enxadrista->clube) ? $enxadrista->clube->getFullName() : "Sem Clube";

            $results[] = $item;
        }


        $total = Enxadrista::where(function ($q1) use ($request) {
            $q1->where([
                ["id", "like", "%" . $request->input("q") . "%"],
            ])
            ->orWhere([
                ["name", "like", "%" . $request->input("q") . "%"],
            ])
            ->orWhere(function ($q1) use ($request) {
                $q1->whereHas("documentos", function ($q2) use ($request) {
                    $q2->where([
                        ["numero", "=", $request->input("q")]
                    ]);
                });
            });
        })
        ->whereDoesntHave("configs", function ($q1) {
            $q1->where([["key", "=", "united_to"]]);
        })->count();


        return response()->json(["ok"=>1, "error"=>0, "players" => $results, "result" => ($total > 30) ? true : false]);
    }
}
