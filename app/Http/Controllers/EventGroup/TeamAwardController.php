<?php

namespace App\Http\Controllers\EventGroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\Process\TeamAwardCalculatorController;

use Auth;

use App\GrupoEvento;


class TeamAwardController extends Controller
{
    public function classificar_page($grupo_evento_id)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionEventByPerfil($id,[7])) {
            return redirect("/");
        }

        $retornos = array();
        $grupo_evento = GrupoEvento::find($grupo_evento_id);
        return view("grupoevento.times.classificar", compact("grupo_evento"));
    }

    public function classificar_call($grupo_evento_id, $time_awards_id, $action)
    {
        $user = Auth::user();
        if (!$user->hasPermissionGlobal() && !$user->hasPermissionEventByPerfil($id,[7])) {
            return redirect("/");
        }

        $retornos = array();
        $grupoevento = GrupoEvento::find($grupo_evento_id);
        if(!$grupoevento){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Grupo de Evento não encontrado."]);
        }
        if($grupoevento->event_team_awards()->where([["id","=",$time_awards_id]])->count() == 0){
            return response()->json(["ok" => 0, "error" => 1, "message" => "Classificação de Time não encontrada."]);
        }
        $time_award = $grupoevento->event_team_awards()->where([["id","=",$time_awards_id]])->first();
        try {
            switch ($action) {
                case 1:
                    TeamAwardCalculatorController::sum_scores(null, $grupoevento, $time_award);
                    break;
                case 2:
                    TeamAwardCalculatorController::generate_tiebreaks(null, $grupoevento, $time_award);
                    break;
                case 3:
                    TeamAwardCalculatorController::classificate_teams(null, $grupoevento, $time_award);
            }
            return response()->json(["ok" => 1, "error" => 0]);
        } catch (Exception $e) {
            return response()->json(["ok" => 0, "error" => 1]);
        }
    }
}
