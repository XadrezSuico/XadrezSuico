<?php

namespace App\Http\Controllers\Player;

use App\Enxadrista;
use App\Http\Controllers\Controller;
use App\PlayerTitle;
use App\Title;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TitleController extends Controller
{
    public function __construct()
    {
        return $this->middleware("auth");
    }
    public function list($enxadrista_id)
    {
        // $enxadristas = Enxadrista::all();
        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        if(Enxadrista::where([["id","=",$enxadrista_id]])->count() == 0){
            return response()->json(["ok"=>0,"error"=>1,"message"=>"Enxadrista não encontrado."]);
        }
        $enxadrista = Enxadrista::where([["id", "=", $enxadrista_id]])->first();

        $titles = array();

        foreach($enxadrista->titles->all() as $player_title){
            $item = array();

            $item["id"] = $player_title->id;
            $item["title"] = $player_title->title->toAPIObject(true);

            $titles[] = $item;
        }

        return response()->json(["ok"=>1,"error"=>0,"player_titles"=> $titles]);
    }

    public function add($enxadrista_id, Request $request)
    {

        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        if (Enxadrista::where([["id", "=", $enxadrista_id]])->count() == 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Enxadrista não encontrado."]);
        }
        $enxadrista = Enxadrista::where([["id", "=", $enxadrista_id]])->first();

        if (!$request->has("titles_id")) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Título não enviado."]);
        }
        if (!($request->titles_id > 0)) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Título não enviado."]);
        }


        if (Title::where([["id", "=", $request->titles_id]])->count() == 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Título não encontrado."]);
        }
        $title = Title::where([["id", "=", $request->titles_id]])->first();

        if ($enxadrista->titles()->where([["titles_id", "=", $title->id]])->count() > 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Enxadrista já possuí o título."]);
        }

        $player_title = new PlayerTitle;
        $player_title->players_id = $enxadrista->id;
        $player_title->titles_id = $title->id;
        $player_title->save();

        return response()->json(["ok" => 1, "error" => 0]);
    }
    public function delete($enxadrista_id, $player_titles_id)
    {

        $user = Auth::user();
        if (!($user->hasPermissionGlobal() || $user->hasPermissionGlobalbyPerfil([9]))) {
            return redirect("/");
        }

        if (Enxadrista::where([["id", "=", $enxadrista_id]])->count() == 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Enxadrista não encontrado."]);
        }
        $enxadrista = Enxadrista::where([["id", "=", $enxadrista_id]])->first();

        if ($enxadrista->titles()->where([["id", "=", $player_titles_id]])->count() == 0) {
            return response()->json(["ok" => 0, "error" => 1, "message" => "Enxadrista não possui o título."]);
        }

        $player_title = $enxadrista->titles()->where([["id", "=", $player_titles_id]])->first();
        $player_title->delete();

        return response()->json(["ok" => 1, "error" => 0]);
    }
}
