<?php

namespace App\Http\Controllers\External\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;
use App\Clube;
use Illuminate\Support\Facades\Auth;

class TeamAwardController extends Controller
{
    public function standings($events_id)
    {
        $event = Evento::find($events_id);

        if(Auth::check()){
            $user = Auth::user();

            if ($user->hasPermissionGlobal() || $user->hasPermissionEventByPerfil($event->id,[7])) {
                $team_awards = $event->event_team_awards->all();
            }
        }

        if(!isset($team_awards)){
            $team_awards = $event->event_team_awards()->where([["is_public", "=", true]])->get();
        }


        return view("evento.publico.team_award.standings", compact("event","team_awards"));
    }
    public function list($events_id, $team_awards_id)
    {
        $event = Evento::find($events_id);
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasPermissionGlobal() || $user->hasPermissionEventByPerfil($event->id, [7])) {
                $team_award = $event->event_team_awards()->where([["id", "=", $team_awards_id]])->first();
            }
        }

        if (!isset($team_award)) {
            $team_award = $event->event_team_awards()->where([["id", "=", $team_awards_id], ["is_public", "=", true]])->first();
        }
        return view("evento.publico.team_award.list", compact("event", "team_award"));
    }
    public function see_team_score($events_id, $team_awards_id, $clubs_id)
    {
        $event = Evento::find($events_id);
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasPermissionGlobal() || $user->hasPermissionEventByPerfil($event->id, [7])) {
                $team_award = $event->event_team_awards()->where([["id", "=", $team_awards_id]])->first();
            }
        }

        if (!isset($team_award)) {
            $team_award = $event->event_team_awards()->where([["id", "=", $team_awards_id], ["is_public", "=", true]])->first();
        }
        $team_score = $team_award->team_scores()->where([["clubs_id","=",$clubs_id]])->first();
        $team = Clube::find($clubs_id);

        if ($event && $team_award && $team) {
            return view("evento.publico.team_award.team", compact("event", "team_award", "team_score", "team"));
        }
        return redirect()->back();
    }
}
