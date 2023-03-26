<?php

namespace App\Http\Controllers\External\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;
use App\Clube;

class TeamAwardController extends Controller
{
    public function standings($events_id)
    {
        $event = Evento::find($events_id);
        return view("evento.publico.team_award.standings", compact("event"));
    }
    public function list($events_id, $team_awards_id)
    {
        $event = Evento::find($events_id);
        $team_award = $event->event_team_awards()->where([["id","=",$team_awards_id]])->first();
        return view("evento.publico.team_award.list", compact("event", "team_award"));
    }
    public function see_team_score($events_id, $team_awards_id, $clubs_id)
    {
        $event = Evento::find($events_id);
        $team_award = $event->event_team_awards()->where([["id","=",$team_awards_id]])->first();
        $team_score = $team_award->team_scores()->where([["clubs_id","=",$clubs_id]])->first();
        $team = Clube::find($clubs_id);

        if ($event && $team_award && $team) {
            return view("evento.publico.team_award.team", compact("event", "team_award", "team_score", "team"));
        }
        return redirect()->back();
    }
}
