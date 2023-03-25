<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Process\TeamAwardCalculatorController;

use Auth;

use App\Evento;

class TeamAwardController extends Controller
{
    public function classificar_page($evento_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionEventByPerfil($id,[7])) {
            return redirect("/");
        }

        $retornos = array();
        $evento = Evento::find($evento_id);
        return view("evento.times.classificar", compact("evento"));
    }

    public function classificar_call($evento_id, $time_awards_id, $action)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionEventByPerfil($id,[7])) {
            return redirect("/");
        }

        $retornos = array();
        $evento = Evento::find($evento_id);
        if(!$evento){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Evento não encontrado."]);
        }
        if($evento->event_team_awards()->where([["id","=",$time_awards_id]])->count() == 0){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Classificação de Time não encontrada."]);
        }
        $time_award = $evento->event_team_awards()->where([["id","=",$time_awards_id]])->first();
        try {
            switch ($action) {
                case 1:
                    TeamAwardCalculatorController::sum_scores($evento, $time_award);
                    break;
                case 2:
                    TeamAwardCalculatorController::generate_tiebreaks($evento, $time_award);
                    break;
                case 3:
                    TeamAwardCalculatorController::classificate_teams($evento, $time_award);
            }
            return response()->json(["ok" => 1, "error" => 0]);
        } catch (Exception $e) {
            return response()->json(["ok" => 0, "error" => 1]);
        }
    }
}
