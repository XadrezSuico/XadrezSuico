<?php

namespace App\Http\Controllers\API\Player;

use App\Http\Controllers\Controller;
use App\Title;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    public function list_select2(Request $request){

        $titles = Title::where(function ($q1) use ($request) {
            $q1->where([
                ["id", "=", $request->input("q")],
            ])
            ->orWhere([
                ["name", "like", "%" . $request->input("q") . "%"],
            ])
            ->orWhere([
                ["abbr", "like", "%" . $request->input("q") . "%"],
            ])
            ->orWhereHas("entity",function($q2) use ($request){
                $q2->where([
                    ["id", "=", $request->input("q")],
                ])
                ->orWhere([
                    ["name", "like", "%" . $request->input("q") . "%"],
                ])
                ->orWhere([
                    ["abbr", "like", "%" . $request->input("q") . "%"],
                ]);
            });
        })
        ->orderBy("id", "ASC")
        ->limit(30)
        ->get();

        $results = array();
        foreach ($titles as $title) {
            $results[] = array("id" => $title->id, "text" => $title->id . " - [" . $title->entity->abbr . "] " . $title->abbr . " - ".$title->name);
        }
        return response()->json(["results" => $results, "pagination" => true]);
    }
}
