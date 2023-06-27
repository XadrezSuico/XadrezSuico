<?php

namespace App\Http\Controllers\External\EventGroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\GrupoEvento;
use App\Clube;

class TeamAwardController extends Controller
{
    public function standings($events_id)
    {
        $event_group = GrupoEvento::find($events_id);
        return view("grupoevento.publico.team_award.standings", compact("event_group"));
    }
    public function list($events_id, $team_awards_id)
    {
        $event_group = GrupoEvento::find($events_id);
        $team_award = $event_group->event_team_awards()->where([["id","=",$team_awards_id]])->first();
        return view("grupoevento.publico.team_award.list", compact("event_group", "team_award"));
    }
    public function see_team_score($events_id, $team_awards_id, $clubs_id)
    {
        $event_group = GrupoEvento::find($events_id);
        $team_award = $event_group->event_team_awards()->where([["id","=",$team_awards_id]])->first();
        $team_score = $team_award->team_scores()->where([["clubs_id","=",$clubs_id]])->first();
        $team = Clube::find($clubs_id);

        if ($event_group && $team_award && $team) {
            return view("grupoevento.publico.team_award.team", compact("event_group", "team_award", "team_score", "team"));
        }
        return redirect()->back();
    }
}
